<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Refund;

use ArrayObject;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\RefundTransfer;

interface UnzerRefundExpanderInterface
{
    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \ArrayObject|array<\Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfersCollectionForRefund
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    public function expandRefundWithUnzerRefundCollection(
        RefundTransfer $refundTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer,
        ArrayObject $expenseTransfersCollectionForRefund
    ): RefundTransfer;
}
