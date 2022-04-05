<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

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
     * @var \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface
     */
    protected $unzerCredentialsResolver;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpensesRefundStrategyResolver\UnzerExpensesRefundStrategyResolverInterface
     */
    protected $unzerExpensesRefundStrategyResolver;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface
     */
    protected $unzerRefundAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Mapper\UnzerMarketplaceRefundMapperInterface
     */
    protected $unzerMarketplaceRefundMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface
     */
    protected $unzerPaymentAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface
     */
    protected $unzerPaymentSaver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface $unzerCredentialsResolver
     * @param \SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpensesRefundStrategyResolver\UnzerExpensesRefundStrategyResolverInterface $unzerExpensesRefundStrategyResolver
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface $unzerRefundAdapter
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface $unzerRepository
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Mapper\UnzerMarketplaceRefundMapperInterface $unzerMarketplaceRefundMapper
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface $unzerPaymentMapper
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface $unzerPaymentSaver
     */
    public function __construct(
        UnzerCredentialsResolverInterface $unzerCredentialsResolver,
        UnzerExpensesRefundStrategyResolverInterface $unzerExpensesRefundStrategyResolver,
        UnzerConfig $unzerConfig,
        UnzerRefundAdapterInterface $unzerRefundAdapter,
        UnzerRepositoryInterface $unzerRepository,
        UnzerMarketplaceRefundMapperInterface $unzerMarketplaceRefundMapper,
        UnzerPaymentMapperInterface $unzerPaymentMapper,
        UnzerPaymentAdapterInterface $unzerPaymentAdapter,
        UnzerPaymentSaverInterface $unzerPaymentSaver
    ) {
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
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return void
     */
    public function refund(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $paymentUnzerTransfer = $this->unzerRepository->findPaymentUnzerByOrderReference($orderTransfer->getOrderReference());
        if ($paymentUnzerTransfer === null) {
            throw new UnzerException(sprintf('Unzer payment for order reference %s not found.', $orderTransfer->getOrderReference()));
        }

        $groupRefundItemTransfersByParticipantIds = $this->getRefundItemsGroupedByParticipantIds($paymentUnzerTransfer, $refundTransfer);
        $paymentUnzerTransactionCollectionTransfer = $this->getPaymentUnzerTransactionCollectionTransfer(
            $paymentUnzerTransfer,
            array_keys($groupRefundItemTransfersByParticipantIds),
        );

        foreach ($groupRefundItemTransfersByParticipantIds as $participantId => $itemCollectionTransfer) {
            $chargeId = $this->getChargeIdByParticipantId($paymentUnzerTransactionCollectionTransfer, $participantId);
            $refundTransfer->addUnzerRefund($this->createUnzerRefundTransfer($paymentUnzerTransfer, $itemCollectionTransfer, $chargeId));
        }

        $refundTransfer = $this->applyExpensesRefundStrategy($refundTransfer, $orderTransfer, $salesOrderItemIds);
        $this->applyRefundChanges($paymentUnzerTransfer, $refundTransfer, $salesOrderItemIds);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     *
     * @return array<string, \Generated\Shared\Transfer\ItemCollectionTransfer>
     */
    protected function getRefundItemsGroupedByParticipantIds(PaymentUnzerTransfer $paymentUnzerTransfer, RefundTransfer $refundTransfer): array
    {
        $paymentUnzerOrderItemsCollection = $this->unzerRepository->getPaymentUnzerOrderItemCollectionByOrderId(
            $paymentUnzerTransfer->getOrderIdOrFail(),
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
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemsCollection
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
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
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\ItemCollectionTransfer $itemCollectionTransfer
     * @param string $chargeId
     *
     * @return \Generated\Shared\Transfer\UnzerRefundTransfer
     */
    protected function createUnzerRefundTransfer(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        ItemCollectionTransfer $itemCollectionTransfer,
        string $chargeId
    ): UnzerRefundTransfer {
        return (new UnzerRefundTransfer())
            ->setIsMarketplace(true)
            ->setPaymentId($paymentUnzerTransfer->getPaymentIdOrFail())
            ->setChargeId($chargeId)
            ->setItems(
                $this->unzerMarketplaceRefundMapper
                    ->mapItemCollectionTransferToUnzerRefundItemCollection($itemCollectionTransfer, new UnzerRefundItemCollectionTransfer())
                    ->getUnzerRefundItems(),
            );
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param array|string $participantIds
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer
     */
    protected function getPaymentUnzerTransactionCollectionTransfer(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        array $participantIds
    ): PaymentUnzerTransactionCollectionTransfer {
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
                    ->addStatus(UnzerConstants::TRANSACTION_STATUS_SUCCESS),
            );

        return $this->unzerRepository
            ->findPaymentUnzerTransactionCollectionByCriteria($paymentUnzerTransactionCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer $paymentUnzerTransactionCollectionTransfer
     * @param string $participantId
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return string
     */
    protected function getChargeIdByParticipantId(
        PaymentUnzerTransactionCollectionTransfer $paymentUnzerTransactionCollectionTransfer,
        string $participantId
    ): string {
        foreach ($paymentUnzerTransactionCollectionTransfer->getPaymentUnzerTransactions() as $paymentUnzerTransactionTransfer) {
            if (
                $paymentUnzerTransactionTransfer->getParticipantId() === null
                || $paymentUnzerTransactionTransfer->getParticipantIdOrFail() === $participantId
            ) {
                return $paymentUnzerTransactionTransfer->getTransactionIdOrFail();
            }
        }

        throw new UnzerException(sprintf('Unzer Charge Id not found for Participant ID %s', $participantId));
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array $salesOrderItemIds
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    protected function applyExpensesRefundStrategy(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): RefundTransfer
    {
        $unzerExpensesRefundStrategy = $this->unzerExpensesRefundStrategyResolver->resolveRefundStrategy($this->unzerConfig->getExpensesRefundStrategyKey());

        return $unzerExpensesRefundStrategy->prepareUnzerRefund($refundTransfer, $orderTransfer, $salesOrderItemIds);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
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
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    protected function getUnzerKeypair(string $keypairId): UnzerKeypairTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())->addKeypairId($keypairId),
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
        array $salesOrderItemIds
    ): void {
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
