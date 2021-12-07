<?php

namespace SprykerEco\Zed\Unzer\Business\Payment\Filter;

use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

abstract class AbstractUnzerPaymentMethodFilter
{
    /**
     * @var string
     */
    protected const MARKETPLACE_PLACEHOLDER = 'Marketplace';
    /**
     * @var string
     */
    protected const MAIN_SELLER_KEY = 'main';


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
        return strpos($paymentMethodTransfer->getMethodName(), $this->config->getProviderName()) !== false;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return bool
     */
    protected function isMarketplace(PaymentMethodTransfer $paymentMethodTransfer): bool
    {
        return strpos($paymentMethodTransfer->getMethodName(), static::MARKETPLACE_PLACEHOLDER) !== false;
    }
}
