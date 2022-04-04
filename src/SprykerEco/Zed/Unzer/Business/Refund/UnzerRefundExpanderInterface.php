<?php

namespace SprykerEco\Zed\Unzer\Business\Refund;

use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\RefundTransfer;

interface UnzerRefundExpanderInterface
{
    /**
     * @param RefundTransfer $refundTransfer
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \ArrayObject|array<ExpenseTransfer> $expenseTransfersCollectionForRefund
     *
     * @return RefundTransfer
     */
    public function expandRefundWithUnzerRefundCollection(
        RefundTransfer $refundTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer,
        \ArrayObject $expenseTransfersCollectionForRefund
    ): RefundTransfer;
}
