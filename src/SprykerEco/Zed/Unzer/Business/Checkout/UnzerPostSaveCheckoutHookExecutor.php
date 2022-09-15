<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as SharedUnzerConfig;
use SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface;

class UnzerPostSaveCheckoutHookExecutor implements UnzerCheckoutHookExecutorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface
     */
    protected UnzerPaymentProcessorResolverInterface $unzerPaymentProcessorResolver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface $unzerPaymentProcessorResolver
     */
    public function __construct(UnzerPaymentProcessorResolverInterface $unzerPaymentProcessorResolver)
    {
        $this->unzerPaymentProcessorResolver = $unzerPaymentProcessorResolver;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function execute(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer
    {
        if (!$this->hasUnzerPayment($quoteTransfer)) {
            return $checkoutResponseTransfer;
        }

        $paymentMethod = $quoteTransfer->getPaymentOrFail()->getPaymentSelectionOrFail();
        $paymentProcessor = $this->unzerPaymentProcessorResolver->resolvePaymentProcessor($paymentMethod);

        return $paymentProcessor->processOrderPayment($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function hasUnzerPayment(QuoteTransfer $quoteTransfer): bool
    {
        return $quoteTransfer->getPayment() !== null && $quoteTransfer->getPaymentOrFail()->getPaymentProvider() === SharedUnzerConfig::PAYMENT_PROVIDER_NAME;
    }
}
