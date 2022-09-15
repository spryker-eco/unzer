<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerAuthorizeAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function authorizePayment(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): UnzerPaymentTransfer;
}
