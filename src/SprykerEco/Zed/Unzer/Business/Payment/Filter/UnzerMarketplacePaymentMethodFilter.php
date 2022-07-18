<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Filter;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;

class UnzerMarketplacePaymentMethodFilter extends AbstractUnzerPaymentMethodFilter implements UnzerPaymentMethodFilterInterface
{
    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function filterPaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        QuoteTransfer $quoteTransfer
    ): PaymentMethodsTransfer {
        if (!$this->hasUnzerPaymentMethod($paymentMethodsTransfer)) {
            return $paymentMethodsTransfer;
        }

        if ($quoteTransfer->getUnzerCredentials() && $quoteTransfer->getUnzerCredentialsOrFail()->getType() === UnzerConstants::UNZER_CREDENTIALS_TYPE_STANDARD) {
            return $this->getStandardUnzerPaymentMethods($paymentMethodsTransfer);
        }

        if ($this->hasMultipleMerchants($quoteTransfer)) {
            return $this->getMarketplaceUnzerPaymentMethods($paymentMethodsTransfer);
        }

        return $this->getMarketplacePrioritizedUnzerPaymentMethods($paymentMethodsTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    protected function getMarketplaceUnzerPaymentMethods(PaymentMethodsTransfer $paymentMethodsTransfer): PaymentMethodsTransfer
    {
        $filteredPaymentMethods = new ArrayObject(
            array_filter((array)$paymentMethodsTransfer->getMethods(), function (PaymentMethodTransfer $paymentMethodTransfer) {
                return !$this->isUnzerPaymentProvider($paymentMethodTransfer) || $this->isMarketplaceUnzerPaymentMethod($paymentMethodTransfer);
            }),
        );

        return $paymentMethodsTransfer->setMethods($filteredPaymentMethods);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    protected function getStandardUnzerPaymentMethods(PaymentMethodsTransfer $paymentMethodsTransfer): PaymentMethodsTransfer
    {
        $filteredPaymentMethods = new ArrayObject(
            array_filter((array)$paymentMethodsTransfer->getMethods(), function (PaymentMethodTransfer $paymentMethodTransfer) {
                return !$this->isUnzerPaymentProvider($paymentMethodTransfer) || !$this->isMarketplaceUnzerPaymentMethod($paymentMethodTransfer);
            }),
        );

        return $paymentMethodsTransfer->setMethods($filteredPaymentMethods);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    protected function getMarketplacePrioritizedUnzerPaymentMethods(PaymentMethodsTransfer $paymentMethodsTransfer): PaymentMethodsTransfer
    {
        $filteredPaymentMethods = new ArrayObject(
            array_filter((array)$paymentMethodsTransfer->getMethods(), function (PaymentMethodTransfer $paymentMethodTransfer) use ($paymentMethodsTransfer) {
                return !$this->isUnzerPaymentProvider($paymentMethodTransfer) || !$this->hasPrioritizedMarketplaceUnzerPaymentMethod($paymentMethodsTransfer, $paymentMethodTransfer);
            }),
        );

        return $paymentMethodsTransfer->setMethods($filteredPaymentMethods);
    }
}
