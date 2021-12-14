<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Filter;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
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
        $filteredPaymentMethodTransferCollection = new ArrayObject();

        $hasMultipleMerchants = $this->hasMultipleMerchants($quoteTransfer);
        if ($hasMultipleMerchants === true) {
            return $paymentMethodsTransfer;
        }

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            if ($this->isUnzerPaymentProvider($paymentMethodTransfer) && $this->isMarketplace($paymentMethodTransfer)) {
                continue;
            }

            $filteredPaymentMethodTransferCollection->append($paymentMethodTransfer);
        }

        $paymentMethodsTransfer->setMethods($filteredPaymentMethodTransferCollection);

        return $paymentMethodsTransfer;
    }
}
