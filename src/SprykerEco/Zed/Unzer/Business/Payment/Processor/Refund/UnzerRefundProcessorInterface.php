<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;

interface UnzerRefundProcessorInterface
{
    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return void
     */
    public function refund(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): void;
}
