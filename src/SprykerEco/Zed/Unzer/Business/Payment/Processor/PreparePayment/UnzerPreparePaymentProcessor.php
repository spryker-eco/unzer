<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerBasketAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\ExpenseDistributor\UnzerExpenseDistributorInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface;

class UnzerPreparePaymentProcessor implements UnzerPreparePaymentProcessorInterface
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
     * @var \SprykerEco\Zed\Unzer\Business\Checkout\ExpenseDistributor\UnzerExpenseDistributorInterface
     */
    protected $unzerExpensesDistributor;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface $unzerCheckoutMapper
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerBasketAdapterInterface $unzerBasketAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Checkout\ExpenseDistributor\UnzerExpenseDistributorInterface $unzerExpensesDistributor
     */
    public function __construct(
        UnzerCheckoutMapperInterface $unzerCheckoutMapper,
        UnzerBasketAdapterInterface $unzerBasketAdapter,
        UnzerExpenseDistributorInterface $unzerExpensesDistributor
    ) {
        $this->unzerCheckoutMapper = $unzerCheckoutMapper;
        $this->unzerBasketAdapter = $unzerBasketAdapter;
        $this->unzerExpensesDistributor = $unzerExpensesDistributor;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function prepareUnzerPaymentTransfer(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): UnzerPaymentTransfer
    {
        $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->setOrderId($saveOrderTransfer->getOrderReference());
        $unzerPaymentTransfer = $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail();
        $unzerBasket = $this->createUnzerBasket($quoteTransfer, $unzerPaymentTransfer->getUnzerKeypairOrFail());

        return $unzerPaymentTransfer->setBasket($unzerBasket)
            ->setCurrency($quoteTransfer->getCurrencyOrFail()->getCode())
            ->setAmountTotal($quoteTransfer->getTotalsOrFail()->getGrandTotal());
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
        if ($quoteTransfer->getExpenses()->count() > 0) {
            $unzerBasketTransfer = $this->unzerExpensesDistributor->distributeExpensesBetweenQuoteItems($quoteTransfer, $unzerBasketTransfer);
        }

        if ($quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getIsMarketplace()) {
            return $this->unzerBasketAdapter->createMarketplaceBasket($unzerBasketTransfer, $unzerKeypairTransfer);
        }

        return $this->unzerBasketAdapter->createBasket($unzerBasketTransfer, $unzerKeypairTransfer);
    }
}
