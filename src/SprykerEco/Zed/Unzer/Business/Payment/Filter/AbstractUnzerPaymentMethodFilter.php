<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Filter;

use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as SharedUnzerConfig;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\UnzerConfig;

abstract class AbstractUnzerPaymentMethodFilter
{
    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected UnzerConfig $unzerConfig;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(UnzerConfig $unzerConfig)
    {
        $this->unzerConfig = $unzerConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return bool
     */
    protected function isUnzerPaymentProvider(PaymentMethodTransfer $paymentMethodTransfer): bool
    {
        return strpos($paymentMethodTransfer->getPaymentMethodKeyOrFail(), $this->unzerConfig->getPaymentProviderType()) !== false;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return bool
     */
    protected function isMarketplaceUnzerPaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): bool
    {
        return $this->isUnzerPaymentProvider($paymentMethodTransfer) && strpos($paymentMethodTransfer->getPaymentMethodKeyOrFail(), SharedUnzerConfig::PLATFORM_MARKETPLACE) !== false;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function hasMultipleMerchants(QuoteTransfer $quoteTransfer): bool
    {
        $merchantReferences = [];

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $merchantReference = $itemTransfer->getMerchantReference();

            if (!$merchantReference && !in_array(UnzerConstants::MAIN_SELLER_REFERENCE, $merchantReferences, true)) {
                $merchantReferences[] = UnzerConstants::MAIN_SELLER_REFERENCE;

                continue;
            }

            if ($merchantReference && !in_array($merchantReference, $merchantReferences, true)) {
                $merchantReferences[] = $merchantReference;
            }
        }

        return count($merchantReferences) > 1;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     *
     * @return bool
     */
    protected function hasUnzerPaymentMethod(PaymentMethodsTransfer $paymentMethodsTransfer): bool
    {
        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            if ($this->isUnzerPaymentProvider($paymentMethodTransfer)) {
                return true;
            }
        }

        return false;
    }
}
