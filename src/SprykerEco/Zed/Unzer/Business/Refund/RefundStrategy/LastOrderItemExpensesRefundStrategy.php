<?php

namespace SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Refund\UnzerRefundExpanderInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;

class LastOrderItemExpensesRefundStrategy extends AbstractExpensesRefundStrategy implements UnzerExpensesRefundStrategyInterface
{
    /**
     * @var UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @var UnzerRefundExpanderInterface
     */
    protected $unzerRefundExpander;

    /**
     * @param UnzerRepositoryInterface $unzerRepository
     * @param UnzerRefundExpanderInterface $unzerRefundExpander
     */
    public function __construct(
        UnzerRepositoryInterface $unzerRepository,
        UnzerRefundExpanderInterface $unzerRefundExpander
    )
    {
        $this->unzerRepository = $unzerRepository;
        $this->unzerRefundExpander = $unzerRefundExpander;
    }

    /**
     * @param RefundTransfer $refundTransfer
     * @param OrderTransfer $orderTransfer
     * @param array $salesOrderItemIds
     *
     * @return RefundTransfer
     */
    public function prepareUnzerRefund(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): RefundTransfer
    {
        $paymentUnzerTransfer = $this->unzerRepository->findPaymentUnzerByOrderReference($orderTransfer->getOrderReference());
        if ($paymentUnzerTransfer === null) {
            throw new UnzerException(sprintf('Unzer payment for order reference %s not found.', $orderTransfer->getOrderReference()));
        }

        $expenseTransfersCollectionForRefund = $this->collectExpenseTransfersForRefund($orderTransfer, $salesOrderItemIds);
        if ($expenseTransfersCollectionForRefund->count() === 0) {
            return $refundTransfer;
        }

        return $this->unzerRefundExpander->expandRefundWithUnzerRefundCollection($refundTransfer, $paymentUnzerTransfer, $expenseTransfersCollectionForRefund);
    }


    /**
     * @param OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return \ArrayObject
     */
    protected function collectExpenseTransfersForRefund(OrderTransfer $orderTransfer, array $salesOrderItemIds): \ArrayObject
    {
        $paymentUnzerOrderItemCollectionTransfer = $this->unzerRepository->getPaymentUnzerOrderItemCollectionByOrderId($orderTransfer->getOrderReferenceOrFail());
        $totalItemsCount = $orderTransfer->getItems()->count();
        $expenseTransferCollectionForRefund = new \ArrayObject();

        foreach ($orderTransfer->getItems() as $orderItemTransfer) {
            if (in_array($orderItemTransfer->getIdSalesOrderItemOrFail(), $salesOrderItemIds, true)) {
                $totalItemsCount--;

                continue;
            }

            $itemAlreadyRefunded = $this->isPaymentUnzerOrderItemAlreadyRefunded(
                $paymentUnzerOrderItemCollectionTransfer,
                $orderItemTransfer->getIdSalesOrderItemOrFail()
            );

            if ($itemAlreadyRefunded) {
                $totalItemsCount--;
            }
        }

        if ($totalItemsCount > 0) {
            return $expenseTransferCollectionForRefund;
        }

        foreach ($orderTransfer->getExpenses() as $expenseTransfer) {
            $expenseTransferCollectionForRefund->append($expenseTransfer);
        }

        return $expenseTransferCollectionForRefund;
    }
}
