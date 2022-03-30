<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
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
        $paymentUnzerTransfer = $this->unzerRepository->findPaymentUnzerByOrderReference($orderTransfer->getOrderReference());
        if ($paymentUnzerTransfer === null) {
            throw new UnzerException('Unzer Payment not found for order reference: ' . $orderTransfer->getOrderReference());
        }

        $unzerPaymentTransfer = $this->unzerPaymentMapper
            ->mapPaymentUnzerTransferToUnzerPaymentTransfer($paymentUnzerTransfer, new UnzerPaymentTransfer());
        $unzerPaymentTransfer->setUnzerKeypair($this->getUnzerKeyPair($unzerPaymentTransfer));

        $paymentUnzerOrderItemCollectionTransfer = $this->unzerRepository
            ->getPaymentUnzerOrderItemCollectionByOrderId($unzerPaymentTransfer->getOrderId());

        $authIdsGroupedByParticipantId = $this->groupAuthorizeIdsByParticipantIds($paymentUnzerTransfer);
        $orderItemsGroupedByParticipantId = $this->groupOrderItemsByParticipantId($paymentUnzerOrderItemCollectionTransfer, $orderTransfer, $salesOrderItemIds);
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
     * @return array<string, string>
     */
    protected function groupAuthorizeIdsByParticipantIds(PaymentUnzerTransfer $paymentUnzerTransfer): array
    {
        $paymentUnzerTransactionCollectionTransfer = $this->getPaymentUnzerTransactionCollection($paymentUnzerTransfer);

        $authIdsGroupedByParticipant = [];
        foreach ($paymentUnzerTransactionCollectionTransfer->getPaymentUnzerTransactions() as $paymentUnzerTransactionTransfer) {
            if ($paymentUnzerTransactionTransfer->getType() === 'authorize') {
                $authIdsGroupedByParticipant[$paymentUnzerTransactionTransfer->getParticipantId()] = $paymentUnzerTransactionTransfer->getTransactionId();
            }
        }

        return $authIdsGroupedByParticipant;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer
     */
    protected function getPaymentUnzerTransactionCollection(PaymentUnzerTransfer $paymentUnzerTransfer): PaymentUnzerTransactionCollectionTransfer
    {
        $paymentUnzerTransactionCriteriaTransfer = (new PaymentUnzerTransactionCriteriaTransfer())->setPaymentUnzerTransactionConditions(
            (new PaymentUnzerTransactionConditionsTransfer())->addFkPaymentUnzerId($paymentUnzerTransfer->getIdPaymentUnzer()),
        );

        return $this->unzerRepository
            ->findPaymentUnzerTransactionCollectionByCriteria($paymentUnzerTransactionCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return array<string, \Generated\Shared\Transfer\ItemCollectionTransfer>
     */
    protected function groupOrderItemsByParticipantId(
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
        OrderTransfer $orderTransfer,
        array $salesOrderItemIds
    ): array {
        $orderItemsGroupedByParticipant = [];
        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            if (!in_array($paymentUnzerOrderItem->getIdSalesOrderItem(), $salesOrderItemIds, true)) {
                continue;
            }

            foreach ($orderTransfer->getItems() as $itemTransfer) {
                if ($itemTransfer->getIdSalesOrderItem() === $paymentUnzerOrderItem->getIdSalesOrderItem()) {
                    $itemCollectionTransfer = $orderItemsGroupedByParticipant[$paymentUnzerOrderItem->getParticipantId()] ?? new ItemCollectionTransfer();
                    $itemCollectionTransfer->addItem($itemTransfer);
                    $orderItemsGroupedByParticipant[$paymentUnzerOrderItem->getParticipantId()] = $itemCollectionTransfer;
                    $itemTransfer->setUnzerParticipantId($paymentUnzerOrderItem->getParticipantId());

                    break;
                }
            }
        }

        return $orderItemsGroupedByParticipant;
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
        if ($orderTransfer->getExpenses()->count() === 0 || $this->countItemsChargedByParticipantId($paymentUnzerOrderItemCollectionTransfer, $participantId) > 0) {
            return $unzerChargeTransfer;
        }

        $expensesAmount = 0;
        foreach ($orderTransfer->getExpenses() as $expenseTransfer) {
            foreach ($orderTransfer->getItems() as $itemTransfer) {
                $itemTransferFkSalesExpense = $itemTransfer->getShipmentOrFail()->getMethodOrFail()->getFkSalesExpense();
                $expenseTransferFkSalesExpense = $expenseTransfer->getShipmentOrFail()->getMethodOrFail()->getFkSalesExpense();
                if ($itemTransfer->getUnzerParticipantId() === $participantId && $itemTransferFkSalesExpense === $expenseTransferFkSalesExpense) {
                    $expensesAmount += $expenseTransfer->getSumGrossPrice();

                    break;
                }
            }
        }

        return $unzerChargeTransfer->setAmount($unzerChargeTransfer->getAmount() + $expensesAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param string $participantId
     *
     * @return int
     */
    protected function countItemsChargedByParticipantId(
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
        string $participantId
    ): int {
        $counter = 0;
        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            if (
                $paymentUnzerOrderItem->getParticipantId() === $participantId
                && $paymentUnzerOrderItem->getStatus() === UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED
            ) {
                $counter++;
            }
        }

        return $counter;
    }
}
