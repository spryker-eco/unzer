<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerBasketAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

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
    protected function prepareUnzerPaymentTransfer(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): UnzerPaymentTransfer
    {
        $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->setOrderId($saveOrderTransfer->getOrderReference());
        $unzerPaymentTransfer = $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail();

        $unzerBasket = $this->createUnzerBasket($quoteTransfer, $unzerPaymentTransfer->getUnzerKeypairOrFail());
        $amountTotal = (int)$quoteTransfer->getTotalsOrFail()->getGrandTotal() / UnzerConstants::INT_TO_FLOAT_DIVIDER;
        $unzerPaymentTransfer->setBasket($unzerBasket);
        $unzerPaymentTransfer->setCurrency($quoteTransfer->getCurrencyOrFail()->getCode());
        $unzerPaymentTransfer->setAmountTotal((int)$amountTotal);

        return $unzerPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    protected function createUnzerBasket(QuoteTransfer $quoteTransfer, UnzerKeypairTransfer $unzerKeypairTransfer): UnzerBasketTransfer
    {
        $unzerBasketTransfer = $this->unzerCheckoutMapper->mapQuoteTransferToUnzerBasketTransfer($quoteTransfer, new UnzerBasketTransfer());

        return $this->unzerBasketAdapter->createBasket($unzerBasketTransfer, $unzerKeypairTransfer);
    }
}
