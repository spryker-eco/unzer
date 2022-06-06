<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Oms\Command;

use Generated\Shared\Transfer\OrderTransfer;

interface UnzerOmsCommandInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return void
     */
    public function execute(OrderTransfer $orderTransfer, array $salesOrderItemIds): void;
}
