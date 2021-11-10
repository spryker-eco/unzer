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
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerMarketplacePaymentMethodFilter implements UnzerPaymentMethodFilterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $config;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $config
     */
    public function __construct(UnzerConfig $config)
    {
        $this->config = $config;
    }

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
        $filteredPaymentMethodTransfersCollection = new ArrayObject();

        $hasMultipleMerchants = $this->hasMultipleMerchants($quoteTransfer);
        if ($hasMultipleMerchants === false) {
            return $paymentMethodsTransfer;
        }

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            if ($this->isUnzerPaymentProvider($paymentMethodTransfer) && !$this->isMarketplace($paymentMethodTransfer)) {
                continue;
            }

            $filteredPaymentMethodTransfersCollection->append($paymentMethodTransfer);
        }

        $paymentMethodsTransfer->setMethods($filteredPaymentMethodTransfersCollection);

        return $paymentMethodsTransfer;
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
            if ($merchantReference !== null && !in_array($merchantReference, $merchantReferences, true)) {
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
        return strpos($paymentMethodTransfer->getMethodName(), 'Marketplace') !== false;
    }
}
