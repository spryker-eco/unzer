<?php

namespace SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;

interface UnzerExpensesRefundStrategyInterface
{
    /**
     * @param RefundTransfer $refundTransfer
     * @param OrderTransfer $orderTransfer
     * @param array $salesOrderItemIds
     *
     * @return RefundTransfer
     */
    public function prepareUnzerRefund(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): RefundTransfer;
}
