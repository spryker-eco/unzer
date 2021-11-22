<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Generated\Shared\Transfer\UnzerRefundTransfer;

interface UnzerRefundAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerRefundTransfer $unzerRefundTransfer
     * @param UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return void
     */
    public function refundPayment(
        UnzerRefundTransfer $unzerRefundTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): void;
}
