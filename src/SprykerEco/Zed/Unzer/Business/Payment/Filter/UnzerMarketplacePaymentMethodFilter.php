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
        $hasMultipleMerchants = $this->quoteMerchantChecker->hasMultipleMerchants($quoteTransfer);
        $filteredPaymentMethods = $this->getStandardUnzerPaymentMethods($paymentMethodsTransfer);

        if ($hasMultipleMerchants) {
            $filteredPaymentMethods = $this->getMarketplaceUnzerPaymentMethods($paymentMethodsTransfer);
        }

        return $paymentMethodsTransfer->setMethods($filteredPaymentMethods);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     *
     * @return \ArrayObject<array-key, \Generated\Shared\Transfer\PaymentMethodTransfer>
     */
    protected function getMarketplaceUnzerPaymentMethods(PaymentMethodsTransfer $paymentMethodsTransfer): ArrayObject
    {
        return new ArrayObject(
            array_filter((array)$paymentMethodsTransfer->getMethods(), function (PaymentMethodTransfer $paymentMethodTransfer) {
                return !$this->isUnzerPaymentProvider($paymentMethodTransfer) || $this->isMarketplaceUnzerPaymentMethod($paymentMethodTransfer);
            }),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     *
     * @return \ArrayObject<array-key, \Generated\Shared\Transfer\PaymentMethodTransfer>
     */
    protected function getStandardUnzerPaymentMethods(PaymentMethodsTransfer $paymentMethodsTransfer): ArrayObject
    {
        return new ArrayObject(
            array_filter((array)$paymentMethodsTransfer->getMethods(), function (PaymentMethodTransfer $paymentMethodTransfer) {
                return !$this->isUnzerPaymentProvider($paymentMethodTransfer) || !$this->isMarketplaceUnzerPaymentMethod($paymentMethodTransfer);
            }),
        );
    }
}
