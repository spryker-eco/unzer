<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

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
