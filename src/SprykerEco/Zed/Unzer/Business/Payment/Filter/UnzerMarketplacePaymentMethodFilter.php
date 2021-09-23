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
        $result = new ArrayObject();

        $hasMultipleMerchants = $this->getQuoteHasMultipleMerchants($quoteTransfer);
        if ($hasMultipleMerchants === false) {
            return $paymentMethodsTransfer;
        }

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethod) {
            if ($this->isPaymentProviderUnzer($paymentMethod) && !$this->isMarketplace($paymentMethod)) {
                continue;
            }

            $result->append($paymentMethod);
        }

        $paymentMethodsTransfer->setMethods($result);

        return $paymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function getQuoteHasMultipleMerchants(QuoteTransfer $quoteTransfer): bool
    {
        $merchants = [];
        foreach ($quoteTransfer->getItems() as $item) {
            $merchantReference = $item->getMerchantReference();
            if ($merchantReference !== null && !in_array($merchantReference, $merchants)) {
                $merchants[] = $merchantReference;
            }
        }

        return count($merchants) > 1;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return bool
     */
    protected function isPaymentProviderUnzer(PaymentMethodTransfer $paymentMethodTransfer): bool
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
