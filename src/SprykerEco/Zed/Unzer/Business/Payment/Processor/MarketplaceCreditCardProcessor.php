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
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerAuthorizeAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerBasketAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerChargeProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment\UnzerPreparePaymentProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface;

class MarketplaceCreditCardProcessor implements UnzerChargeablePaymentProcessorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerAuthorizeAdapterInterface
     */
    protected $unzerAuthorizeAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface
     */
    protected $unzerPaymentAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerChargeProcessorInterface
     */
    protected $unzerChargeProcessor;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface
     */
    protected $unzerRefundProcessor;

    /**
     * @var UnzerPreparePaymentProcessorInterface
     */
    protected $unzerPreparePaymentProcessor;

    /**
     * @var UnzerCheckoutMapperInterface
     */
    protected $unzerCheckoutMapper;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerAuthorizeAdapterInterface $unzerAuthorizeAdapter
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerChargeProcessorInterface $unzerChargeProcessor
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface $unzerRefundProcessor
     * @param UnzerPreparePaymentProcessorInterface $unzerPreparePaymentProcessor
     * @param UnzerCheckoutMapperInterface $unzerCheckoutMapper
     */
    public function __construct(
        UnzerAuthorizeAdapterInterface $unzerAuthorizeAdapter,
        UnzerPaymentAdapterInterface $unzerPaymentAdapter,
        UnzerChargeProcessorInterface $unzerChargeProcessor,
        UnzerRefundProcessorInterface $unzerRefundProcessor,
        UnzerPreparePaymentProcessorInterface $unzerPreparePaymentProcessor,
        UnzerCheckoutMapperInterface $unzerCheckoutMapper
    ) {
        $this->unzerAuthorizeAdapter = $unzerAuthorizeAdapter;
        $this->unzerPaymentAdapter = $unzerPaymentAdapter;
        $this->unzerChargeProcessor = $unzerChargeProcessor;
        $this->unzerRefundProcessor = $unzerRefundProcessor;
        $this->unzerPreparePaymentProcessor = $unzerPreparePaymentProcessor;
        $this->unzerCheckoutMapper = $unzerCheckoutMapper;
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
        $unzerPaymentTransfer->setPaymentResource(
            $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getPaymentResourceOrFail(),
        );
        $unzerPaymentTransfer = $this->unzerAuthorizeAdapter->authorizePayment($unzerPaymentTransfer);

        return $this->unzerPaymentAdapter->getPaymentInfo($unzerPaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array $salesOrderItemIds
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
     * @param array $salesOrderItemIds
     *
     * @return void
     */
    public function processRefund(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $this->unzerRefundProcessor->refund($refundTransfer, $orderTransfer, $salesOrderItemIds);
    }
}
