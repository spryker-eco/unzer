<?php

namespace SprykerEco\Zed\Unzer\Business\Checkout\ExpensesDistributor;

use Generated\Shared\Transfer\QuoteTransfer;

interface UnzerExpensesDistributorInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return QuoteTransfer
     */
    public function distributeExpensesBetweenQuoteItems(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
