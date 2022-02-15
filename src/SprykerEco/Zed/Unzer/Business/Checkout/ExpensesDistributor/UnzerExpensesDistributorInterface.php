<?php

namespace SprykerEco\Zed\Unzer\Business\Checkout\ExpensesDistributor;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;

interface UnzerExpensesDistributorInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param UnzerBasketTransfer $unzerBasketTransfer
     *
     * @return UnzerBasketTransfer
     */
    public function distributeExpensesBetweenQuoteItems(QuoteTransfer $quoteTransfer, UnzerBasketTransfer $unzerBasketTransfer): UnzerBasketTransfer;
}
