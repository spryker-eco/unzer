<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Saver;

use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;

interface UnzerRefundPaymentSaverInterface
{
    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return void
     */
    public function saveUnzerPaymentDetails(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer,
        array $salesOrderItemIds
    ): void;
}
