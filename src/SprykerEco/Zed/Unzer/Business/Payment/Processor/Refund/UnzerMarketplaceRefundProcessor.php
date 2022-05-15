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
use Generated\Shared\Transfer\UnzerRefundItemCollectionTransfer;
use Generated\Shared\Transfer\UnzerRefundTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Mapper\UnzerMarketplaceRefundMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Saver\UnzerRefundPaymentSaverInterface;
use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpenseRefundStrategyResolver\UnzerExpenseRefundStrategyResolverInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerMarketplaceRefundProcessor implements UnzerRefundProcessorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface
     */
    protected $unzerCredentialsResolver;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpenseRefundStrategyResolver\UnzerExpenseRefundStrategyResolverInterface
     */
    protected $unzerExpenseRefundStrategyResolver;

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
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Saver\UnzerRefundPaymentSaverInterface
     */
    protected $unzerRefundPaymentSaver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface $unzerCredentialsResolver
     * @param \SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpenseRefundStrategyResolver\UnzerExpenseRefundStrategyResolverInterface $unzerExpenseRefundStrategyResolver
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface $unzerRefundAdapter
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface $unzerRepository
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Mapper\UnzerMarketplaceRefundMapperInterface $unzerMarketplaceRefundMapper
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Saver\UnzerRefundPaymentSaverInterface $unzerRefundPaymentSaver
     */
    public function __construct(
        UnzerCredentialsResolverInterface $unzerCredentialsResolver,
        UnzerExpenseRefundStrategyResolverInterface $unzerExpenseRefundStrategyResolver,
        UnzerRefundAdapterInterface $unzerRefundAdapter,
        UnzerRepositoryInterface $unzerRepository,
        UnzerMarketplaceRefundMapperInterface $unzerMarketplaceRefundMapper,
        UnzerRefundPaymentSaverInterface $unzerRefundPaymentSaver
    ) {
        $this->unzerCredentialsResolver = $unzerCredentialsResolver;
        $this->unzerExpenseRefundStrategyResolver = $unzerExpenseRefundStrategyResolver;
        $this->unzerRefundAdapter = $unzerRefundAdapter;
        $this->unzerRepository = $unzerRepository;
        $this->unzerMarketplaceRefundMapper = $unzerMarketplaceRefundMapper;
        $this->unzerRefundPaymentSaver = $unzerRefundPaymentSaver;
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
        $paymentUnzerTransfer = $this->unzerRepository->findPaymentUnzerByOrderReference($orderTransfer->getOrderReferenceOrFail());
        if ($paymentUnzerTransfer === null) {
            throw new UnzerException(sprintf('Unzer payment for order reference %s not found.', $orderTransfer->getOrderReferenceOrFail()));
        }

        $refundItemTransfersGroupedByParticipantId = $this->getRefundItemsGroupedByParticipantId($paymentUnzerTransfer, $refundTransfer);
        $paymentUnzerTransactionCollectionTransfer = $this->getPaymentUnzerTransactionCollectionTransfer(
            $paymentUnzerTransfer,
            array_keys($refundItemTransfersGroupedByParticipantId),
        );

        foreach ($refundItemTransfersGroupedByParticipantId as $participantId => $itemCollectionTransfer) {
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
    protected function getRefundItemsGroupedByParticipantId(PaymentUnzerTransfer $paymentUnzerTransfer, RefundTransfer $refundTransfer): array
    {
        $paymentUnzerOrderItemsCollection = $this->unzerRepository->getPaymentUnzerOrderItemCollectionByOrderId(
            $paymentUnzerTransfer->getOrderIdOrFail(),
        );

        $groupedRefundItems = [];

        foreach ($refundTransfer->getItems() as $itemTransfer) {
            $participantId = $this->getParticipantIdByItem($paymentUnzerOrderItemsCollection, $itemTransfer);
            if (!isset($groupedRefundItems[$participantId])) {
                $groupedRefundItems[$participantId] = new ItemCollectionTransfer();
            }
            $groupedRefundItems[$participantId]->addItem($itemTransfer->setUnzerParticipantId($participantId));
        }

        return $groupedRefundItems;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemsCollection
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return string
     */
    protected function getParticipantIdByItem(PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemsCollection, ItemTransfer $itemTransfer): string
    {
        foreach ($paymentUnzerOrderItemsCollection->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            if ($paymentUnzerOrderItem->getIdSalesOrderItemOrFail() === $itemTransfer->getIdSalesOrderItemOrFail()) {
                return $paymentUnzerOrderItem->getParticipantIdOrFail();
            }
        }

        throw new UnzerException(sprintf('No participantId found for Item %s', $itemTransfer->getNameOrFail()));
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
     * @param array<string> $participantIds
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
                    ->addPaymentUnzerId($paymentUnzerTransfer->getIdPaymentUnzerOrFail())
                    ->setParticipantIds($participantIds)
                    ->addType(UnzerConstants::TRANSACTION_TYPE_CHARGE)
                    ->addStatus(UnzerConstants::TRANSACTION_STATUS_SUCCESS),
            );

        return $this->unzerRepository
            ->getPaymentUnzerTransactionCollectionByCriteria($paymentUnzerTransactionCriteriaTransfer);
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
     * @param array<int> $salesOrderItemIds
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    protected function applyExpensesRefundStrategy(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): RefundTransfer
    {
        $unzerExpensesRefundStrategy = $this->unzerExpenseRefundStrategyResolver->resolveRefundStrategyFromConfig();

        return $unzerExpensesRefundStrategy->prepareUnzerRefundTransfer($refundTransfer, $orderTransfer, $salesOrderItemIds);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param array<int> $salesOrderItemIds
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

        $this->unzerRefundPaymentSaver->saveUnzerPaymentDetails(
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
}
