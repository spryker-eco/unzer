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

class LastShipmentItemExpensesRefundStrategy extends AbstractExpensesRefundStrategy implements UnzerExpensesRefundStrategyInterface
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
        $paymentUnzerTransfer = $this->unzerRepository->findPaymentUnzerByOrderReference($orderTransfer->getOrderReference());
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
     * @return \ArrayObject<\Generated\Shared\Transfer\ExpenseTransfer>
     */
    protected function collectExpenseTransfersForRefund(OrderTransfer $orderTransfer, array $salesOrderItemIds): ArrayObject
    {
        $orderItemsGroupedByIdSalesShipment = $this->getOrderItemsGroupedByIdSalesShipment($orderTransfer);
        $paymentUnzerOrderItemCollection = $this->unzerRepository->getPaymentUnzerOrderItemCollectionByOrderId($orderTransfer->getOrderReferenceOrFail());
        $idsSalesShipmentForRefund = $this->detectShipmentsForRefund($orderItemsGroupedByIdSalesShipment, $paymentUnzerOrderItemCollection, $salesOrderItemIds);

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
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return array<int>
     */
    protected function detectShipmentsForRefund(
        array $orderItemsGroupedByIdSalesShipment,
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
        array $salesOrderItemIds
    ): array {
        $result = [];
        foreach ($orderItemsGroupedByIdSalesShipment as $idSalesShipment => $itemTransfers) {
            $totalItemsCount = count($itemTransfers);
            foreach ($itemTransfers as $itemTransfer) {
                if (in_array($itemTransfer->getIdSalesOrderItemOrFail(), $salesOrderItemIds, true)) {
                    $totalItemsCount--;

                    continue;
                }

                $itemAlreadyRefunded = $this
                    ->isPaymentUnzerOrderItemAlreadyRefunded(
                        $paymentUnzerOrderItemCollectionTransfer,
                        $itemTransfer->getIdSalesOrderItemOrFail(),
                    );

                if ($itemAlreadyRefunded) {
                    $totalItemsCount--;
                }
            }

            if ($totalItemsCount === 0) {
                $result[] = $idSalesShipment;
            }
        }

        return $result;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array<int, array<\Generated\Shared\Transfer\ItemTransfer>>
     */
    protected function getOrderItemsGroupedByIdSalesShipment(OrderTransfer $orderTransfer)
    {
        $indexedItems = [];
        foreach ($orderTransfer->getItems() as $itemTransfer) {
            $idSalesShipment = $itemTransfer->getShipmentOrFail()->getIdSalesShipmentOrFail();
            $indexedItems[$idSalesShipment][] = $itemTransfer;
        }

        return $indexedItems;
    }
}
