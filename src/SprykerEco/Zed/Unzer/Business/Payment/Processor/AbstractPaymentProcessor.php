<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerBasketAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface;

abstract class AbstractPaymentProcessor
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface
     */
    protected $unzerCheckoutMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerBasketAdapterInterface
     */
    protected $unzerBasketAdapter;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface $unzerCheckoutMapper
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerBasketAdapterInterface $unzerBasketAdapter
     */
    public function __construct(UnzerCheckoutMapperInterface $unzerCheckoutMapper, UnzerBasketAdapterInterface $unzerBasketAdapter)
    {
        $this->unzerCheckoutMapper = $unzerCheckoutMapper;
        $this->unzerBasketAdapter = $unzerBasketAdapter;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function prepareUnzerPaymentTransfer(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer)
    {
        $quoteTransfer = $this->setUnzerPaymentOrderId($quoteTransfer, $saveOrderTransfer);
        $unzerPaymentTransfer = $quoteTransfer->getPayment()->getUnzerPaymentOrFail();

        $unzerBasket = $this->createUnzerBasket($quoteTransfer);
        $unzerPaymentTransfer->setBasket($unzerBasket);

        $unzerPaymentTransfer->setCurrency($quoteTransfer->getCurrency()->getCode());
        $unzerPaymentTransfer->setAmountTotal($quoteTransfer->getTotals()->getGrandTotal() / 100);

        return $unzerPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function setUnzerPaymentOrderId(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): QuoteTransfer
    {
        $quoteTransfer->getPayment()->getUnzerPayment()->setOrderId($saveOrderTransfer->getOrderReference());

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    protected function createUnzerBasket(QuoteTransfer $quoteTransfer): UnzerBasketTransfer
    {
        $unzerBasketTransfer = $this->unzerCheckoutMapper->mapQuoteTransferToUnzerBasketTransfer($quoteTransfer, new UnzerBasketTransfer());

        return $this->unzerBasketAdapter->createBasket($unzerBasketTransfer);
    }
}
