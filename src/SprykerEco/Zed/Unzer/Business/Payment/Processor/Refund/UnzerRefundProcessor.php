<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionConditionsTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCriteriaTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerRefundTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Saver\UnzerRefundPaymentSaverInterface;
use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpenseRefundStrategyResolver\UnzerExpenseRefundStrategyResolverInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerRefundProcessor implements UnzerRefundProcessorInterface
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
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Saver\UnzerRefundPaymentSaverInterface
     */
    protected $unzerRefundPaymentSaver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface $unzerCredentialsResolver
     * @param \SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpenseRefundStrategyResolver\UnzerExpenseRefundStrategyResolverInterface $unzerExpenseRefundStrategyResolver
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface $unzerRefundAdapter
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface $unzerRepository
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Saver\UnzerRefundPaymentSaverInterface $unzerRefundPaymentSaver
     */
    public function __construct(
        UnzerCredentialsResolverInterface $unzerCredentialsResolver,
        UnzerExpenseRefundStrategyResolverInterface $unzerExpenseRefundStrategyResolver,
        UnzerRefundAdapterInterface $unzerRefundAdapter,
        UnzerRepositoryInterface $unzerRepository,
        UnzerRefundPaymentSaverInterface $unzerRefundPaymentSaver
    ) {
        $this->unzerCredentialsResolver = $unzerCredentialsResolver;
        $this->unzerExpenseRefundStrategyResolver = $unzerExpenseRefundStrategyResolver;
        $this->unzerRefundAdapter = $unzerRefundAdapter;
        $this->unzerRepository = $unzerRepository;
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

        $refundItemsGroupedByChargeId = $this->getRefundItemsGroupedByChargeId($paymentUnzerTransfer, $refundTransfer);
        foreach ($refundItemsGroupedByChargeId as $chargeId => $itemCollectionTransfer) {
            $refundTransfer->addUnzerRefund($this->createUnzerRefundTransfer($paymentUnzerTransfer, $itemCollectionTransfer, $chargeId));
        }

        $refundTransfer = $this->applyExpensesRefundStrategy($refundTransfer, $orderTransfer, $salesOrderItemIds);

        $this->applyRefundChanges($paymentUnzerTransfer, $refundTransfer, $salesOrderItemIds);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return string
     */
    protected function getUnzerPaymentChargeId(PaymentUnzerTransfer $paymentUnzerTransfer): string
    {
        $paymentUnzerTransactionCriteriaTransfer = (new PaymentUnzerTransactionCriteriaTransfer())
            ->setPaymentUnzerTransactionConditions(
                (new PaymentUnzerTransactionConditionsTransfer())
                    ->addPaymentUnzerId($paymentUnzerTransfer->getIdPaymentUnzerOrFail())
                    ->addType(UnzerConstants::TRANSACTION_TYPE_CHARGE)
                    ->addStatus(UnzerConstants::TRANSACTION_STATUS_SUCCESS),
            );

        $paymentUnzerTransactionCollectionTransfer = $this->unzerRepository
            ->getPaymentUnzerTransactionCollectionByCriteria($paymentUnzerTransactionCriteriaTransfer);

        if ($paymentUnzerTransactionCollectionTransfer->getPaymentUnzerTransactions()->count() === 0) {
            throw new UnzerException(sprintf('Unzer transactions for Payment ID %s not found.', $paymentUnzerTransfer->getPaymentIdOrFail()));
        }

        $paymentUnzerTransaction = $paymentUnzerTransactionCollectionTransfer->getPaymentUnzerTransactions()->getIterator()->current();

        return $paymentUnzerTransaction->getTransactionIdOrFail();
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
        $refundAmount = 0;
        foreach ($itemCollectionTransfer->getItems() as $itemTransfer) {
            $refundAmount += $itemTransfer->getRefundableAmountOrFail();
        }

        return (new UnzerRefundTransfer())
            ->setPaymentId($paymentUnzerTransfer->getPaymentIdOrFail())
            ->setChargeId($chargeId)
            ->setAmount($refundAmount / UnzerConstants::INT_TO_FLOAT_DIVIDER);
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

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     *
     * @return array<string, \Generated\Shared\Transfer\ItemCollectionTransfer>
     */
    protected function getRefundItemsGroupedByChargeId(PaymentUnzerTransfer $paymentUnzerTransfer, RefundTransfer $refundTransfer): array
    {
        $paymentUnzerOrderItemsCollection = $this->unzerRepository->getPaymentUnzerOrderItemCollectionByOrderId(
            $paymentUnzerTransfer->getOrderIdOrFail(),
        );

        $groupedRefundItems = [];
        if (!$paymentUnzerTransfer->getIsAuthorizableOrFail()) {
            $chargeId = $this->getUnzerPaymentChargeId($paymentUnzerTransfer);
            $groupedRefundItems[$chargeId] = (new ItemCollectionTransfer())->setItems($refundTransfer->getItems());

            return $groupedRefundItems;
        }

        foreach ($refundTransfer->getItems() as $itemTransfer) {
            $chargeId = $this->getChargeIdByIdSalesOrderItem($paymentUnzerOrderItemsCollection, $itemTransfer->getIdSalesOrderItemOrFail());

            if (!isset($groupedRefundItems[$chargeId])) {
                $groupedRefundItems[$chargeId] = new ItemCollectionTransfer();
            }
            $groupedRefundItems[$chargeId]->addItem($itemTransfer);
        }

        return $groupedRefundItems;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param int $idSalesOrderItem
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return string
     */
    protected function getChargeIdByIdSalesOrderItem(
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
        int $idSalesOrderItem
    ): string {
        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            if ($paymentUnzerOrderItem->getIdSalesOrderItemOrFail() === $idSalesOrderItem) {
                return $paymentUnzerOrderItem->getChargeIdOrFail();
            }
        }

        throw new UnzerException(sprintf('Unzer Charge Id not found for Sales order item Id %s', $idSalesOrderItem));
    }
}
