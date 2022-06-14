<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentResourceAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\DirectCharge\UnzerDirectChargeProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment\UnzerPreparePaymentProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface;

class DirectPaymentProcessor implements UnzerPaymentProcessorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentResourceAdapterInterface
     */
    protected $unzerPaymentResourceAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface
     */
    protected $unzerRefundProcessor;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment\UnzerPreparePaymentProcessorInterface
     */
    protected $unzerPreparePaymentProcessor;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface
     */
    protected $unzerCheckoutMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface
     */
    protected $unzerPaymentAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\DirectCharge\UnzerDirectChargeProcessorInterface
     */
    protected $unzerDirectChargeProcessor;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentResourceAdapterInterface $unzerPaymentResourceAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface $unzerRefundProcessor
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment\UnzerPreparePaymentProcessorInterface $unzerPreparePaymentProcessor
     * @param \SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface $unzerCheckoutMapper
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\DirectCharge\UnzerDirectChargeProcessorInterface $unzerDirectChargeProcessor
     */
    public function __construct(
        UnzerPaymentAdapterInterface $unzerPaymentAdapter,
        UnzerPaymentResourceAdapterInterface $unzerPaymentResourceAdapter,
        UnzerRefundProcessorInterface $unzerRefundProcessor,
        UnzerPreparePaymentProcessorInterface $unzerPreparePaymentProcessor,
        UnzerCheckoutMapperInterface $unzerCheckoutMapper,
        UnzerDirectChargeProcessorInterface $unzerDirectChargeProcessor
    ) {
        $this->unzerPaymentAdapter = $unzerPaymentAdapter;
        $this->unzerPaymentResourceAdapter = $unzerPaymentResourceAdapter;
        $this->unzerRefundProcessor = $unzerRefundProcessor;
        $this->unzerPreparePaymentProcessor = $unzerPreparePaymentProcessor;
        $this->unzerCheckoutMapper = $unzerCheckoutMapper;
        $this->unzerDirectChargeProcessor = $unzerDirectChargeProcessor;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function processOrderPayment(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): UnzerPaymentTransfer
    {
        $unzerPaymentTransfer = $this->unzerPreparePaymentProcessor->prepareUnzerPaymentTransfer($quoteTransfer, $saveOrderTransfer);
        $unzerPaymentTransfer->setPaymentResource($this->createUnzerPaymentResource($quoteTransfer));
        $unzerPaymentTransfer = $this->unzerDirectChargeProcessor->charge($unzerPaymentTransfer);

        return $this->unzerPaymentAdapter->getPaymentInfo($unzerPaymentTransfer);
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
