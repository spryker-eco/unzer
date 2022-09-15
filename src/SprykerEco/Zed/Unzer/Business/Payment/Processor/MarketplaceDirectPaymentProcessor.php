<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Zed\Unzer\UnzerConstants;

class MarketplaceDirectPaymentProcessor extends DirectPaymentProcessor
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function processOrderPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer
    {
        $unzerPaymentTransfer = $this->unzerPreparePaymentProcessor->prepareUnzerPaymentTransfer($quoteTransfer, $saveOrderTransfer);
        $unzerPaymentTransfer->setPaymentResource($this->createUnzerPaymentResource($quoteTransfer));
        $unzerPaymentTransfer = $this->unzerDirectChargeProcessor->charge($unzerPaymentTransfer);

        $quoteTransfer->getPaymentOrFail()->setUnzerPayment($unzerPaymentTransfer);
        $this->unzerPaymentUpdater->updateUnzerPaymentDetails($unzerPaymentTransfer, UnzerConstants::OMS_STATUS_PAYMENT_PENDING);

        return $checkoutResponseTransfer->setRedirectUrl($unzerPaymentTransfer->getRedirectUrl())
            ->setIsExternalRedirect(true)
            ->setIsSuccess(true);
    }
}
