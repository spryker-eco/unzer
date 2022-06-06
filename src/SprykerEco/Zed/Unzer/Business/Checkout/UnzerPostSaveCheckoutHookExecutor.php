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
use SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerPostSaveCheckoutHookExecutor implements UnzerCheckoutHookExecutorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface
     */
    protected $unzerPaymentUpdater;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface
     */
    protected $unzerPaymentProcessorResolver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface $unzerPaymentUpdater
     * @param \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface $unzerPaymentProcessorResolver
     */
    public function __construct(
        UnzerPaymentUpdaterInterface $unzerPaymentUpdater,
        UnzerPaymentProcessorResolverInterface $unzerPaymentProcessorResolver
    ) {
        $this->unzerPaymentUpdater = $unzerPaymentUpdater;
        $this->unzerPaymentProcessorResolver = $unzerPaymentProcessorResolver;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function execute(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): void
    {
        if ($quoteTransfer->getPayment() === null) {
            return;
        }

        if ($quoteTransfer->getPaymentOrFail()->getPaymentProvider() !== SharedUnzerConfig::PAYMENT_PROVIDER_NAME) {
            return;
        }

        $paymentMethod = $quoteTransfer->getPaymentOrFail()->getPaymentSelectionOrFail();
        $paymentProcessor = $this->unzerPaymentProcessorResolver->resolvePaymentProcessor($paymentMethod);
        $unzerPaymentTransfer = $paymentProcessor->processOrderPayment($quoteTransfer, $checkoutResponseTransfer->getSaveOrderOrFail());

        $checkoutResponseTransfer
            ->setRedirectUrl($unzerPaymentTransfer->getRedirectUrl())
            ->setIsExternalRedirect(true);
        $quoteTransfer->getPaymentOrFail()->setUnzerPayment($unzerPaymentTransfer);

        $this->unzerPaymentUpdater->updateUnzerPaymentDetails($unzerPaymentTransfer, UnzerConstants::OMS_STATUS_PAYMENT_PENDING);
    }
}
