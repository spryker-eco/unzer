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
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentResourceAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\DirectCharge\UnzerDirectChargeProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment\UnzerPreparePaymentProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class DirectPaymentProcessor implements UnzerPaymentProcessorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentResourceAdapterInterface
     */
    protected UnzerPaymentResourceAdapterInterface $unzerPaymentResourceAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface
     */
    protected UnzerRefundProcessorInterface $unzerRefundProcessor;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment\UnzerPreparePaymentProcessorInterface
     */
    protected UnzerPreparePaymentProcessorInterface $unzerPreparePaymentProcessor;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface
     */
    protected UnzerCheckoutMapperInterface $unzerCheckoutMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface
     */
    protected UnzerPaymentAdapterInterface $unzerPaymentAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\DirectCharge\UnzerDirectChargeProcessorInterface
     */
    protected UnzerDirectChargeProcessorInterface $unzerDirectChargeProcessor;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface
     */
    protected UnzerPaymentUpdaterInterface $unzerPaymentUpdater;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentResourceAdapterInterface $unzerPaymentResourceAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface $unzerRefundProcessor
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment\UnzerPreparePaymentProcessorInterface $unzerPreparePaymentProcessor
     * @param \SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface $unzerCheckoutMapper
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\DirectCharge\UnzerDirectChargeProcessorInterface $unzerDirectChargeProcessor
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface $unzerPaymentUpdater
     */
    public function __construct(
        UnzerPaymentAdapterInterface $unzerPaymentAdapter,
        UnzerPaymentResourceAdapterInterface $unzerPaymentResourceAdapter,
        UnzerRefundProcessorInterface $unzerRefundProcessor,
        UnzerPreparePaymentProcessorInterface $unzerPreparePaymentProcessor,
        UnzerCheckoutMapperInterface $unzerCheckoutMapper,
        UnzerDirectChargeProcessorInterface $unzerDirectChargeProcessor,
        UnzerPaymentUpdaterInterface $unzerPaymentUpdater
    ) {
        $this->unzerPaymentAdapter = $unzerPaymentAdapter;
        $this->unzerPaymentResourceAdapter = $unzerPaymentResourceAdapter;
        $this->unzerRefundProcessor = $unzerRefundProcessor;
        $this->unzerPreparePaymentProcessor = $unzerPreparePaymentProcessor;
        $this->unzerCheckoutMapper = $unzerCheckoutMapper;
        $this->unzerDirectChargeProcessor = $unzerDirectChargeProcessor;
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
        $unzerPaymentTransfer = $this->unzerPreparePaymentProcessor
            ->prepareUnzerPaymentTransfer($quoteTransfer, $checkoutResponseTransfer->getSaveOrderOrFail())
            ->setPaymentResource($this->createUnzerPaymentResource($quoteTransfer));

        $unzerPaymentTransfer = $this->unzerDirectChargeProcessor->charge($unzerPaymentTransfer);
        $unzerPaymentTransfer = $this->unzerPaymentAdapter->getPaymentInfo($unzerPaymentTransfer);

        $quoteTransfer->getPaymentOrFail()->setUnzerPayment($unzerPaymentTransfer);
        $this->unzerPaymentUpdater->updateUnzerPaymentDetails($unzerPaymentTransfer, UnzerConstants::OMS_STATUS_PAYMENT_PENDING);

        return $checkoutResponseTransfer->setRedirectUrl($unzerPaymentTransfer->getRedirectUrl())
            ->setIsExternalRedirect(true)
            ->setIsSuccess(true);
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
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentResourceTransfer
     */
    protected function createUnzerPaymentResource(QuoteTransfer $quoteTransfer): UnzerPaymentResourceTransfer
    {
        $unzerPaymentResourceTransfer = $this->unzerCheckoutMapper
            ->mapQuoteTransferToUnzerPaymentResourceTransfer(
                $quoteTransfer,
                new UnzerPaymentResourceTransfer(),
            );

        return $this->unzerPaymentResourceAdapter->createPaymentResource(
            $unzerPaymentResourceTransfer,
            $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getUnzerKeypairOrFail(),
        );
    }
}
