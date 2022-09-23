<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Checkout;

use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as SharedUnzerConfig;
use SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerPostSaveCheckoutHookExecutor implements UnzerCheckoutHookExecutorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface
     */
    protected UnzerPaymentUpdaterInterface $unzerPaymentUpdater;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface
     */
    protected UnzerPaymentProcessorResolverInterface $unzerPaymentProcessorResolver;

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
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function execute(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer
    {
        if (!$this->hasUnzerPayment($quoteTransfer)) {
            return $checkoutResponseTransfer;
        }

        $paymentMethod = $quoteTransfer->getPaymentOrFail()->getPaymentSelectionOrFail();
        $paymentProcessor = $this->unzerPaymentProcessorResolver->resolvePaymentProcessor($paymentMethod);
        $unzerPaymentTransfer = $paymentProcessor->processOrderPayment($quoteTransfer, $checkoutResponseTransfer->getSaveOrderOrFail());

        if ($unzerPaymentTransfer->getErrors()->count() !== 0) {
            return $this->appendUnzerPaymentErrorTransfersToCheckoutResponseTransfer(
                $unzerPaymentTransfer,
                $checkoutResponseTransfer,
            );
        }

        $quoteTransfer->getPaymentOrFail()->setUnzerPayment($unzerPaymentTransfer);
        $this->unzerPaymentUpdater->updateUnzerPaymentDetails($unzerPaymentTransfer, UnzerConstants::OMS_STATUS_PAYMENT_PENDING);

        return $checkoutResponseTransfer
            ->setRedirectUrl($unzerPaymentTransfer->getRedirectUrl())
            ->setIsExternalRedirect(true);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function appendUnzerPaymentErrorTransfersToCheckoutResponseTransfer(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): CheckoutResponseTransfer {
        foreach ($unzerPaymentTransfer->getErrors() as $unzerPaymentErrorTransfer) {
            $checkoutErrorTransfer = (new CheckoutErrorTransfer())->fromArray(
                $unzerPaymentErrorTransfer->toArray(),
                true,
            );

            $checkoutResponseTransfer->addError($checkoutErrorTransfer);
        }

        return $checkoutResponseTransfer->setIsSuccess(false);
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
