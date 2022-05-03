<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy;

use ArrayObject;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Refund\UnzerRefundExpanderInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class LastShipmentItemExpenseRefundStrategy implements UnzerExpenseRefundStrategyInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Refund\UnzerRefundExpanderInterface
     */
    protected $unzerRefundExpander;

    /**
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface $unzerRepository
     * @param \SprykerEco\Zed\Unzer\Business\Refund\UnzerRefundExpanderInterface $unzerRefundExpander
     */
    public function __construct(
        UnzerRepositoryInterface $unzerRepository,
        UnzerRefundExpanderInterface $unzerRefundExpander
    ) {
        $this->unzerRepository = $unzerRepository;
        $this->unzerRefundExpander = $unzerRefundExpander;
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    public function prepareUnzerRefundTransfer(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): RefundTransfer
    {
        $paymentUnzerTransfer = $this->unzerRepository->findPaymentUnzerByOrderReference((string)$orderTransfer->getOrderReference());
        if ($paymentUnzerTransfer === null) {
            throw new UnzerException(sprintf('Unzer payment for order reference %s not found.', $orderTransfer->getOrderReference()));
        }

        $expenseTransfersForRefund = $this->collectExpenseTransfersForRefund($orderTransfer, $salesOrderItemIds);
        if ($expenseTransfersForRefund->count() === 0) {
            return $refundTransfer;
        }

        return $this->unzerRefundExpander->expandRefundWithUnzerRefundCollection($refundTransfer, $paymentUnzerTransfer, $expenseTransfersForRefund);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer>
     */
    protected function collectExpenseTransfersForRefund(OrderTransfer $orderTransfer, array $salesOrderItemIds): ArrayObject
    {
        $orderItemsGroupedByIdSalesShipment = $this->getOrderItemsGroupedByIdSalesShipment($orderTransfer);
        $idsSalesShipmentForRefund = $this->detectShipmentsForRefund($orderItemsGroupedByIdSalesShipment, $salesOrderItemIds);

        $expenseTransfers = new ArrayObject();
        foreach ($orderTransfer->getExpenses() as $expenseTransfer) {
            if (in_array($expenseTransfer->getShipmentOrFail()->getIdSalesShipmentOrFail(), $idsSalesShipmentForRefund, true)) {
                $expenseTransfers->append($expenseTransfer);
            }
        }

        return $expenseTransfers;
    }

    /**
     * @param array<int, array<\Generated\Shared\Transfer\ItemTransfer>> $orderItemsGroupedByIdSalesShipment
     * @param array<int> $salesOrderItemIds
     *
     * @return array<int>
     */
    protected function detectShipmentsForRefund(
        array $orderItemsGroupedByIdSalesShipment,
        array $salesOrderItemIds
    ): array {
        $idsSalesShipment = [];
        foreach ($orderItemsGroupedByIdSalesShipment as $idSalesShipment => $itemTransfers) {
            if ($this->allItemsAreRefunded($itemTransfers)) {
                continue;
            }

            if ($this->expenseShouldBeRefunded($itemTransfers, $salesOrderItemIds)) {
                $idsSalesShipment[] = $idSalesShipment;
            }
        }

        return $idsSalesShipment;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array<int, array<\Generated\Shared\Transfer\ItemTransfer>>
     */
    protected function getOrderItemsGroupedByIdSalesShipment(OrderTransfer $orderTransfer)
    {
        $groupedItemTransfers = [];
        foreach ($orderTransfer->getItems() as $itemTransfer) {
            $idSalesShipment = $itemTransfer->getShipmentOrFail()->getIdSalesShipmentOrFail();
            $groupedItemTransfers[$idSalesShipment][] = $itemTransfer;
        }

        return $groupedItemTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    protected function isPaymentUnzerOrderItemAlreadyRefunded(
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
        int $idSalesOrderItem
    ): bool {
        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            if (
                $paymentUnzerOrderItem->getIdSalesOrderItemOrFail() === $idSalesOrderItem
                && $paymentUnzerOrderItem->getStatusOrFail() === UnzerConstants::OMS_STATUS_CHARGE_REFUNDED
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     * @param array<int> $salesOrderItemIds
     *
     * @return bool
     */
    protected function expenseShouldBeRefunded(array $itemTransfers, array $salesOrderItemIds): bool
    {
        $itemsCountForRefund = 0;
        foreach ($itemTransfers as $itemTransfer) {
            if (in_array($itemTransfer->getIdSalesOrderItemOrFail(), $salesOrderItemIds, true)) {
                $itemsCountForRefund++;

                continue;
            }

            if ($itemTransfer->getCanceledAmountOrFail() !== 0) {
                $itemsCountForRefund++;
            }
        }

        return $itemsCountForRefund === count($itemTransfers);
    }

    /**
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     *
     * @return bool
     */
    protected function allItemsAreRefunded(array $itemTransfers): bool
    {
        $refundedItemsCount = 0;
        foreach ($itemTransfers as $itemTransfer) {
            if ($itemTransfer->getCanceledAmountOrFail() !== 0) {
                $refundedItemsCount++;
            }
        }

        return $refundedItemsCount === count($itemTransfers);
    }
}
