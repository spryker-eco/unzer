<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerAuthorizeAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerChargeProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment\UnzerPreparePaymentProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class CreditCardProcessor implements UnzerChargeablePaymentProcessorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerAuthorizeAdapterInterface
     */
    protected UnzerAuthorizeAdapterInterface $unzerAuthorizeAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface
     */
    protected UnzerPaymentAdapterInterface $unzerPaymentAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerChargeProcessorInterface
     */
    protected UnzerChargeProcessorInterface $unzerChargeProcessor;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface
     */
    protected UnzerRefundProcessorInterface $unzerRefundProcessor;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment\UnzerPreparePaymentProcessorInterface
     */
    protected UnzerPreparePaymentProcessorInterface $unzerPreparePaymentProcessor;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface
     */
    protected UnzerPaymentUpdaterInterface $unzerPaymentUpdater;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerAuthorizeAdapterInterface $unzerAuthorizeAdapter
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerChargeProcessorInterface $unzerChargeProcessor
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface $unzerRefundProcessor
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment\UnzerPreparePaymentProcessorInterface $unzerPreparePaymentProcessor
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface $unzerPaymentUpdater
     */
    public function __construct(
        UnzerAuthorizeAdapterInterface $unzerAuthorizeAdapter,
        UnzerPaymentAdapterInterface $unzerPaymentAdapter,
        UnzerChargeProcessorInterface $unzerChargeProcessor,
        UnzerRefundProcessorInterface $unzerRefundProcessor,
        UnzerPreparePaymentProcessorInterface $unzerPreparePaymentProcessor,
        UnzerPaymentUpdaterInterface $unzerPaymentUpdater
    ) {
        $this->unzerAuthorizeAdapter = $unzerAuthorizeAdapter;
        $this->unzerPaymentAdapter = $unzerPaymentAdapter;
        $this->unzerChargeProcessor = $unzerChargeProcessor;
        $this->unzerRefundProcessor = $unzerRefundProcessor;
        $this->unzerPreparePaymentProcessor = $unzerPreparePaymentProcessor;
        $this->unzerPaymentUpdater = $unzerPaymentUpdater;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function processOrderPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer
    {
        $unzerPaymentTransfer = $this->prepareUnzerPaymentTransfer($quoteTransfer, $checkoutResponseTransfer->getSaveOrderOrFail());
        $unzerPaymentTransfer = $this->unzerAuthorizeAdapter->authorizePayment($unzerPaymentTransfer, $checkoutResponseTransfer);

        if (!$checkoutResponseTransfer->getIsSuccess()) {
            return $checkoutResponseTransfer;
        }

        $unzerPaymentTransfer = $this->unzerPaymentAdapter->getPaymentInfo($unzerPaymentTransfer);

        $quoteTransfer->getPaymentOrFail()->setUnzerPayment($unzerPaymentTransfer);
        $this->unzerPaymentUpdater->updateUnzerPaymentDetails($unzerPaymentTransfer, UnzerConstants::OMS_STATUS_PAYMENT_PENDING);

        return $checkoutResponseTransfer->setRedirectUrl($unzerPaymentTransfer->getRedirectUrl())
            ->setIsExternalRedirect(true)
            ->setIsSuccess(true);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return void
     */
    public function processCharge(OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $this->unzerChargeProcessor->charge($orderTransfer, $salesOrderItemIds);
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return void
     */
    public function processRefund(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $this->unzerRefundProcessor->refund($refundTransfer, $orderTransfer, $salesOrderItemIds);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function prepareUnzerPaymentTransfer(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    ): UnzerPaymentTransfer {
        $unzerPaymentTransfer = $this->unzerPreparePaymentProcessor->prepareUnzerPaymentTransfer($quoteTransfer, $saveOrderTransfer);
        $unzerPaymentResourceTransfer = $this->getUnzerPaymentResourceFromQuote($quoteTransfer);

        return $unzerPaymentTransfer->setPaymentResource($unzerPaymentResourceTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentResourceTransfer
     */
    protected function getUnzerPaymentResourceFromQuote(QuoteTransfer $quoteTransfer): UnzerPaymentResourceTransfer
    {
        return $quoteTransfer->getPaymentOrFail()->getUnzerCreditCardOrFail()->getPaymentResourceOrFail();
    }
}
