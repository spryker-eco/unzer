<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor;

use Generated\Shared\Transfer\OrderTransfer;

interface UnzerChargeablePaymentProcessorInterface extends UnzerPaymentProcessorInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array $salesOrderItemIds
     *
     * @return void
     */
    public function processCharge(OrderTransfer $orderTransfer, array $salesOrderItemIds): void;
}
