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
use SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerPostSaveCheckoutHook implements UnzerCheckoutHookInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface
     */
    protected $unzerPaymentSaver;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface
     */
    protected $paymentProcessorStrategyResolver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface $unzerPaymentSaver
     * @param \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface $paymentProcessorStrategyResolver
     */
    public function __construct(
        UnzerPaymentSaverInterface $unzerPaymentSaver,
        UnzerPaymentProcessorResolverInterface $paymentProcessorStrategyResolver
    ) {
        $this->unzerPaymentSaver = $unzerPaymentSaver;
        $this->paymentProcessorStrategyResolver = $paymentProcessorStrategyResolver;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function execute(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): void
    {
        if ($quoteTransfer->getPaymentOrFail() === null) {
            return;
        }

        if ($quoteTransfer->getPaymentOrFail()->getPaymentProvider() !== SharedUnzerConfig::PROVIDER_NAME) {
            return;
        }

        $paymentMethod = $quoteTransfer->getPaymentOrFail()->getPaymentSelection();
        $paymentProcessor = $this->paymentProcessorStrategyResolver->resolvePaymentProcessor($paymentMethod);

        $unzerPaymentTransfer = $paymentProcessor->processOrderPayment($quoteTransfer, $checkoutResponseTransfer->getSaveOrder());

        $checkoutResponseTransfer
            ->setRedirectUrl($unzerPaymentTransfer->getRedirectUrl())
            ->setIsExternalRedirect(true);
        $quoteTransfer->getPaymentOrFail()->setUnzerPayment($unzerPaymentTransfer);

        $this->unzerPaymentSaver->saveUnzerPaymentDetails($unzerPaymentTransfer, UnzerConstants::OMS_STATUS_PAYMENT_PENDING);
    }
}
