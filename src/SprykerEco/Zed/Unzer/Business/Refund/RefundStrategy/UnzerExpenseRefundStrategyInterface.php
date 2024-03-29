<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;

interface UnzerExpenseRefundStrategyInterface
{
    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    public function prepareUnzerRefundTransfer(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): RefundTransfer;
}
