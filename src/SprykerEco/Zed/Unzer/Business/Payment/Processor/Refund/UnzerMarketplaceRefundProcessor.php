<?php

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionConditionsTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCriteriaTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Generated\Shared\Transfer\UnzerRefundItemCollectionTransfer;
use Generated\Shared\Transfer\UnzerRefundTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Mapper\UnzerMarketplaceRefundMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface;
use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpensesRefundStrategyResolver\UnzerExpensesRefundStrategyResolverInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerMarketplaceRefundProcessor implements UnzerRefundProcessorInterface
{
    /**
     * @var UnzerCredentialsResolverInterface
     */
    protected $unzerCredentialsResolver;

    /**
     * @var UnzerExpensesRefundStrategyResolverInterface
     */
    protected $unzerExpensesRefundStrategyResolver;

    /**
     * @var UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var UnzerRefundAdapterInterface
     */
    protected $unzerRefundAdapter;

    /**
     * @var UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @var UnzerMarketplaceRefundMapperInterface
     */
    protected $unzerMarketplaceRefundMapper;

    /**
     * @var UnzerPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @var UnzerPaymentAdapterInterface
     */
    protected $unzerPaymentAdapter;

    /**
     * @var UnzerPaymentSaverInterface
     */
    protected $unzerPaymentSaver;

    /**
     * @param UnzerCredentialsResolverInterface $unzerCredentialsResolver
     * @param UnzerExpensesRefundStrategyResolverInterface $unzerExpensesRefundStrategyResolver
     * @param UnzerConfig $unzerConfig
     * @param UnzerRefundAdapterInterface $unzerRefundAdapter
     * @param UnzerRepositoryInterface $unzerRepository
     * @param UnzerMarketplaceRefundMapperInterface $unzerMarketplaceRefundMapper
     * @param UnzerPaymentMapperInterface $unzerPaymentMapper
     * @param UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param UnzerPaymentSaverInterface $unzerPaymentSaver
     */
    public function __construct(
        UnzerCredentialsResolverInterface            $unzerCredentialsResolver,
        UnzerExpensesRefundStrategyResolverInterface $unzerExpensesRefundStrategyResolver,
        UnzerConfig                                  $unzerConfig,
        UnzerRefundAdapterInterface                  $unzerRefundAdapter,
        UnzerRepositoryInterface                     $unzerRepository,
        UnzerMarketplaceRefundMapperInterface        $unzerMarketplaceRefundMapper,
        UnzerPaymentMapperInterface                  $unzerPaymentMapper,
        UnzerPaymentAdapterInterface                 $unzerPaymentAdapter,
        UnzerPaymentSaverInterface                   $unzerPaymentSaver
    )
    {
        $this->unzerCredentialsResolver = $unzerCredentialsResolver;
        $this->unzerExpensesRefundStrategyResolver = $unzerExpensesRefundStrategyResolver;
        $this->unzerConfig = $unzerConfig;
        $this->unzerRefundAdapter = $unzerRefundAdapter;
        $this->unzerRepository = $unzerRepository;
        $this->unzerMarketplaceRefundMapper = $unzerMarketplaceRefundMapper;
        $this->unzerPaymentMapper = $unzerPaymentMapper;
        $this->unzerPaymentAdapter = $unzerPaymentAdapter;
        $this->unzerPaymentSaver = $unzerPaymentSaver;
    }

    /**
     * @param RefundTransfer $refundTransfer
     * @param OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return void
     */
    public function refund(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $paymentUnzerTransfer = $this->unzerRepository->findPaymentUnzerByOrderReference($orderTransfer->getOrderReference());
        if ($paymentUnzerTransfer === null) {
            throw new UnzerException(sprintf('Unzer payment for order reference %s not found.', $orderTransfer->getOrderReference()));
        }

        $groupRefundItemTransfersByParticipantIds = $this->groupRefundItemTransfersByParticipantIds($paymentUnzerTransfer, $refundTransfer);
        $paymentUnzerTransactionCollectionTransfer = $this->getPaymentUnzerTransactionCollectionTransfer(
            $paymentUnzerTransfer,
            array_keys($groupRefundItemTransfersByParticipantIds)
        );

        foreach ($groupRefundItemTransfersByParticipantIds as $participantId => $itemCollectionTransfer) {
            $chargeId = $this->getChargeIdByParticipantId($paymentUnzerTransactionCollectionTransfer, $participantId);
            $refundTransfer->addUnzerRefund($this->createUnzerRefund($paymentUnzerTransfer, $itemCollectionTransfer, $chargeId));
        }

        $refundTransfer = $this->applyExpensesRefundStrategy($refundTransfer, $orderTransfer, $salesOrderItemIds);
        $this->applyRefundChanges($paymentUnzerTransfer, $refundTransfer, $salesOrderItemIds);
    }

    /**
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     * @param RefundTransfer $refundTransfer
     *
     * @return array<string, ItemCollectionTransfer>
     */
    protected function groupRefundItemTransfersByParticipantIds(PaymentUnzerTransfer $paymentUnzerTransfer, RefundTransfer $refundTransfer): array
    {
        $paymentUnzerOrderItemsCollection = $this->unzerRepository->getPaymentUnzerOrderItemCollectionByOrderId(
            $paymentUnzerTransfer->getOrderIdOrFail()
        );

        $result = [];

        foreach ($refundTransfer->getItems() as $itemTransfer) {
            $participantId = $this->findParticipantIdByItem($paymentUnzerOrderItemsCollection, $itemTransfer);
            if (!isset($result[$participantId])) {
                $result[$participantId] = new ItemCollectionTransfer();
            }
            $result[$participantId]->addItem($itemTransfer->setUnzerParticipantId($participantId));
        }

        return $result;
    }

    /**
     * @param PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemsCollection
     * @param ItemTransfer $itemTransfer
     *
     * @return string
     */
    protected function findParticipantIdByItem(PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemsCollection, ItemTransfer $itemTransfer): string
    {
        foreach ($paymentUnzerOrderItemsCollection->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            if ($paymentUnzerOrderItem->getIdSalesOrderItemOrFail() === $itemTransfer->getIdSalesOrderItemOrFail()) {
                return $paymentUnzerOrderItem->getParticipantIdOrFail();
            }
        }
    }

    /**
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     * @param ItemCollectionTransfer $itemCollectionTransfer
     * @param string $chargeId
     *
     * @return UnzerRefundTransfer
     */
    protected function createUnzerRefund(
        PaymentUnzerTransfer   $paymentUnzerTransfer,
        ItemCollectionTransfer $itemCollectionTransfer,
        string                 $chargeId
    ): UnzerRefundTransfer
    {
        return (new UnzerRefundTransfer())
            ->setIsMarketplace(true)
            ->setPaymentId($paymentUnzerTransfer->getPaymentIdOrFail())
            ->setChargeId($chargeId)
            ->setItems(
                $this->unzerMarketplaceRefundMapper
                    ->mapItemCollectionTransferToUnzerRefundItemCollection($itemCollectionTransfer, new UnzerRefundItemCollectionTransfer())
                    ->getUnzerRefundItems()
            );
    }

    /**
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     * @param string $participantId
     *
     * @return PaymentUnzerTransactionCollectionTransfer
     */
    protected function getPaymentUnzerTransactionCollectionTransfer(PaymentUnzerTransfer $paymentUnzerTransfer, array $participantIds): PaymentUnzerTransactionCollectionTransfer
    {
        if (!$paymentUnzerTransfer->getIsAuthorizable()) {
            //Sofort and BankTransfer transactions do not have participantId
            $participantIds = null;
        }

        $paymentUnzerTransactionCriteriaTransfer = (new PaymentUnzerTransactionCriteriaTransfer())
            ->setPaymentUnzerTransactionConditions(
                (new PaymentUnzerTransactionConditionsTransfer())
                    ->addFkPaymentUnzerId($paymentUnzerTransfer->getIdPaymentUnzer())
                    ->setParticipantIds($participantIds)
                    ->addType(UnzerConstants::TRANSACTION_TYPE_CHARGE)
                    ->addStatus(UnzerConstants::TRANSACTION_STATUS_SUCCESS)
            );

        return $this->unzerRepository
            ->findPaymentUnzerTransactionCollectionByCriteria($paymentUnzerTransactionCriteriaTransfer);
    }

    /**
     * @param PaymentUnzerTransactionCollectionTransfer $paymentUnzerTransactionCollectionTransfer
     * @param string $participantId
     *
     * @return string
     */
    protected function getChargeIdByParticipantId(PaymentUnzerTransactionCollectionTransfer $paymentUnzerTransactionCollectionTransfer, string $participantId): string
    {
        foreach ($paymentUnzerTransactionCollectionTransfer->getPaymentUnzerTransactions() as $paymentUnzerTransactionTransfer) {
            if ($paymentUnzerTransactionTransfer->getParticipantId() === null) {
                return $paymentUnzerTransactionTransfer->getTransactionIdOrFail();
            }

            if ($paymentUnzerTransactionTransfer->getParticipantIdOrFail() === $participantId) {
                return $paymentUnzerTransactionTransfer->getTransactionIdOrFail();
            }
        }

        throw new UnzerException(sprintf('Unzer Charge Id not found for Participant ID %s', $participantId));
    }

    /**
     * @param RefundTransfer $refundTransfer
     * @param OrderTransfer $orderTransfer
     * @param array $salesOrderItemIds
     *
     * @return RefundTransfer
     */
    protected function applyExpensesRefundStrategy(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): RefundTransfer
    {
        $unzerExpensesRefundStrategy = $this->unzerExpensesRefundStrategyResolver->resolveRefundStrategy($this->unzerConfig->getExpensesRefundStrategyKey());

        return $unzerExpensesRefundStrategy->prepareUnzerRefund($refundTransfer, $orderTransfer, $salesOrderItemIds);
    }

    /**
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     * @param RefundTransfer $refundTransfer
     * @param array $salesOrderItemIds
     *
     * @return void
     */
    protected function applyRefundChanges(PaymentUnzerTransfer $paymentUnzerTransfer, RefundTransfer $refundTransfer, array $salesOrderItemIds): void
    {
        $unzerKeypairTransfer = $this->getUnzerKeypair($paymentUnzerTransfer->getKeypairIdOrFail());

        foreach ($refundTransfer->getUnzerRefunds() as $unzerRefundTransfer) {
            $this->unzerRefundAdapter->refundPayment(
                $unzerRefundTransfer,
                $unzerKeypairTransfer,
            );
        }

        $this->saveUnzerPaymentDetails(
            $paymentUnzerTransfer,
            $unzerKeypairTransfer,
            $salesOrderItemIds,
        );
    }

    /**
     * @param string $keypairId
     *
     * @return UnzerKeypairTransfer
     */
    protected function getUnzerKeypair(string $keypairId): UnzerKeypairTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())->addKeypairId($keypairId)
            );
        $unzerCredentialsTransfer = $this->unzerCredentialsResolver
            ->resolveUnzerCredentialsByCriteriaTransfer($unzerCredentialsCriteriaTransfer);

        return $unzerCredentialsTransfer->getUnzerKeypairOrFail();
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     * @param array $salesOrderItemIds
     *
     * @return void
     */
    protected function saveUnzerPaymentDetails(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer,
        array                $salesOrderItemIds
    ): void
    {
        $unzerPaymentTransfer = $this->unzerPaymentMapper
            ->mapPaymentUnzerTransferToUnzerPaymentTransfer($paymentUnzerTransfer, new UnzerPaymentTransfer());
        $unzerPaymentTransfer->setUnzerKeypair($unzerKeypairTransfer);
        $unzerPaymentTransfer = $this->unzerPaymentAdapter->getPaymentInfo($unzerPaymentTransfer);

        $this->unzerPaymentSaver->saveUnzerPaymentDetails(
            $unzerPaymentTransfer,
            UnzerConstants::OMS_STATUS_CHARGE_REFUNDED,
            $salesOrderItemIds,
        );
    }
}
