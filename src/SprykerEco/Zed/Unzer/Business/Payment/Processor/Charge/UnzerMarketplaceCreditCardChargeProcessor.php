<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge;

use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionConditionsTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCriteriaTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerChargeTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerMarketplaceCreditCardChargeProcessor extends UnzerCreditCardChargeProcessor
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return void
     */
    public function charge(OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $paymentUnzerTransfer = $this->unzerRepository->findPaymentUnzerByOrderReference($orderTransfer->getOrderReferenceOrFail());
        if ($paymentUnzerTransfer === null) {
            throw new UnzerException(sprintf('Unzer Payment not found for order reference %s', $orderTransfer->getOrderReferenceOrFail()));
        }

        $unzerPaymentTransfer = $this->unzerPaymentMapper
            ->mapPaymentUnzerTransferToUnzerPaymentTransfer($paymentUnzerTransfer, new UnzerPaymentTransfer());
        $unzerPaymentTransfer->setUnzerKeypair($this->getUnzerKeyPair($unzerPaymentTransfer));

        $paymentUnzerOrderItemCollectionTransfer = $this->unzerRepository
            ->getPaymentUnzerOrderItemCollectionByOrderId($unzerPaymentTransfer->getOrderIdOrFail());

        $authIdsGroupedByParticipantId = $this->getAuthorizeIdsIndexedByParticipantIds($paymentUnzerTransfer);
        $orderItemsGroupedByParticipantId = $this->getOrderItemsGroupedByParticipantId($paymentUnzerOrderItemCollectionTransfer, $orderTransfer, $salesOrderItemIds);
        foreach ($orderItemsGroupedByParticipantId as $participantId => $itemCollectionTransfer) {
            $unzerChargeTransfer = $this->createUnzerCharge($unzerPaymentTransfer, $itemCollectionTransfer, $authIdsGroupedByParticipantId[$participantId]);
            $unzerChargeTransfer = $this->addExpensesToMarketplaceUnzerChargeTransfer(
                $unzerChargeTransfer,
                $orderTransfer,
                $paymentUnzerOrderItemCollectionTransfer,
                $participantId,
            );

            $this->unzerChargeAdapter->chargePartialAuthorizablePayment($unzerPaymentTransfer, $unzerChargeTransfer);
        }

        $this->updatePaymentUnzerOrderItemEntities($paymentUnzerOrderItemCollectionTransfer, $salesOrderItemIds);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return array<string, string|null>
     */
    protected function getAuthorizeIdsIndexedByParticipantIds(PaymentUnzerTransfer $paymentUnzerTransfer): array
    {
        $paymentUnzerTransactionCollectionTransfer = $this->getPaymentUnzerTransactionCollection($paymentUnzerTransfer);

        $indexedAuthorizeTransactionIds = [];
        foreach ($paymentUnzerTransactionCollectionTransfer->getPaymentUnzerTransactions() as $paymentUnzerTransactionTransfer) {
            if ($paymentUnzerTransactionTransfer->getType() === UnzerConstants::TRANSACTION_TYPE_AUTHORIZE) {
                $indexedAuthorizeTransactionIds[$paymentUnzerTransactionTransfer->getParticipantId()] = $paymentUnzerTransactionTransfer->getTransactionId();
            }
        }

        return $indexedAuthorizeTransactionIds;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer
     */
    protected function getPaymentUnzerTransactionCollection(PaymentUnzerTransfer $paymentUnzerTransfer): PaymentUnzerTransactionCollectionTransfer
    {
        $paymentUnzerTransactionCriteriaTransfer = (new PaymentUnzerTransactionCriteriaTransfer())->setPaymentUnzerTransactionConditions(
            (new PaymentUnzerTransactionConditionsTransfer())->addPaymentUnzerId($paymentUnzerTransfer->getIdPaymentUnzerOrFail()),
        );

        return $this->unzerRepository
            ->getPaymentUnzerTransactionCollectionByCriteria($paymentUnzerTransactionCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return array<string, \Generated\Shared\Transfer\ItemCollectionTransfer>
     */
    protected function getOrderItemsGroupedByParticipantId(
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
        OrderTransfer $orderTransfer,
        array $salesOrderItemIds
    ): array {
        $groupedOrderItems = [];
        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            if (!in_array($paymentUnzerOrderItem->getIdSalesOrderItem(), $salesOrderItemIds, true)) {
                continue;
            }

            $groupedOrderItems = $this->addOrderItemToGroupByParticipantId(
                $orderTransfer,
                $paymentUnzerOrderItem,
                $groupedOrderItems,
            );
        }

        return $groupedOrderItems;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
     * @param array<string, \Generated\Shared\Transfer\ItemCollectionTransfer> $groupedOrderItems
     *
     * @return array<string, \Generated\Shared\Transfer\ItemCollectionTransfer>
     */
    protected function addOrderItemToGroupByParticipantId(
        OrderTransfer $orderTransfer,
        PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer,
        array $groupedOrderItems
    ): array {
        foreach ($orderTransfer->getItems() as $itemTransfer) {
            if ($itemTransfer->getIdSalesOrderItem() !== $paymentUnzerOrderItemTransfer->getIdSalesOrderItem()) {
                continue;
            }

            $itemCollectionTransfer = $groupedOrderItems[$paymentUnzerOrderItemTransfer->getParticipantId()] ?? new ItemCollectionTransfer();
            $itemCollectionTransfer->addItem($itemTransfer);
            $groupedOrderItems[$paymentUnzerOrderItemTransfer->getParticipantId()] = $itemCollectionTransfer;
            $itemTransfer->setUnzerParticipantId($paymentUnzerOrderItemTransfer->getParticipantId());

            break;
        }

        return $groupedOrderItems;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerChargeTransfer $unzerChargeTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param string $participantId
     *
     * @return \Generated\Shared\Transfer\UnzerChargeTransfer
     */
    protected function addExpensesToMarketplaceUnzerChargeTransfer(
        UnzerChargeTransfer $unzerChargeTransfer,
        OrderTransfer $orderTransfer,
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
        string $participantId
    ): UnzerChargeTransfer {
        if ($orderTransfer->getExpenses()->count() === 0 || $this->orderHasChargedItemsByParticipantId($paymentUnzerOrderItemCollectionTransfer, $participantId)) {
            return $unzerChargeTransfer;
        }

        $expensesAmount = 0;
        foreach ($orderTransfer->getExpenses() as $expenseTransfer) {
            $expensesAmount += $this->getExpensesAmountForOrderExpenseByParticipantId($orderTransfer, $expenseTransfer, $participantId);
        }

        return $unzerChargeTransfer->setAmount($unzerChargeTransfer->getAmount() + $expensesAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @param string $participantId
     *
     * @return int
     */
    protected function getExpensesAmountForOrderExpenseByParticipantId(
        OrderTransfer $orderTransfer,
        ExpenseTransfer $expenseTransfer,
        string $participantId
    ): int {
        $expensesAmount = 0;
        foreach ($orderTransfer->getItems() as $itemTransfer) {
            $itemTransferFkSalesExpense = $itemTransfer->getShipmentOrFail()->getMethodOrFail()->getFkSalesExpense();
            $expenseTransferFkSalesExpense = $expenseTransfer->getShipmentOrFail()->getMethodOrFail()->getFkSalesExpense();
            if ($itemTransfer->getUnzerParticipantId() === $participantId && $itemTransferFkSalesExpense === $expenseTransferFkSalesExpense) {
                $expensesAmount += $expenseTransfer->getSumGrossPrice();

                break;
            }
        }

        return $expensesAmount;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param string $participantId
     *
     * @return bool
     */
    protected function orderHasChargedItemsByParticipantId(
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
        string $participantId
    ): bool {
        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            if (
                $paymentUnzerOrderItem->getParticipantId() === $participantId
                && $paymentUnzerOrderItem->getStatus() === UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED
            ) {
                return true;
            }
        }

        return false;
    }
}
