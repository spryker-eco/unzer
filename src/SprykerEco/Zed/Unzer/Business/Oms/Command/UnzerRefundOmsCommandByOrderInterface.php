<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Oms\Command;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;

interface UnzerRefundOmsCommandByOrderInterface
{
    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int[] $salesOrderItemIds
     *
     * @return void
     */
    public function execute(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): void;
}
