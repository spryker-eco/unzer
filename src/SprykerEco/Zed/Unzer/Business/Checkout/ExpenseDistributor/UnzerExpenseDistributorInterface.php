<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Checkout\ExpenseDistributor;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;

interface UnzerExpenseDistributorInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    public function distributeExpensesBetweenQuoteItems(QuoteTransfer $quoteTransfer, UnzerBasketTransfer $unzerBasketTransfer): UnzerBasketTransfer;
}
