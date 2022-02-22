<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerChargeTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerChargeAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function chargePayment(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer;

    /**
     * @param UnzerPaymentTransfer $unzerPaymentTransfer
     * @param UnzerChargeTransfer $unzerChargeTransfer
     *
     * @return UnzerPaymentTransfer
     */
    public function chargePartialAuthorizablePayment(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        UnzerChargeTransfer $unzerChargeTransfer
    ): UnzerPaymentTransfer;
}
