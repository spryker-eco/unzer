<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Filter;

use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as SharedUnzerConfig;
use SprykerEco\Zed\Unzer\UnzerConfig;

abstract class AbstractUnzerPaymentMethodFilter
{
    /**
     * @var string
     */
    protected const MAIN_SELLER_KEY = 'main';

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(UnzerConfig $unzerConfig)
    {
        $this->unzerConfig = $unzerConfig;
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

            if ($merchantReference === null) {
                $merchantReferences[] = static::MAIN_SELLER_KEY;

                continue;
            }

            if (!in_array($merchantReference, $merchantReferences, true)) {
                $merchantReferences[] = $merchantReference;
            }
        }

        return count($merchantReferences) > 1;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return bool
     */
    protected function isUnzerPaymentProvider(PaymentMethodTransfer $paymentMethodTransfer): bool
    {
        return strpos($paymentMethodTransfer->getPaymentMethodKey(), $this->unzerConfig->getPaymentProviderType()) !== false;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return bool
     */
    protected function isMarketplaceUnzerPaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): bool
    {
        return $this->isUnzerPaymentProvider($paymentMethodTransfer) && strpos($paymentMethodTransfer->getPaymentMethodKey(), SharedUnzerConfig::PLATFORM_MARKETPLACE) !== false;
    }
}
