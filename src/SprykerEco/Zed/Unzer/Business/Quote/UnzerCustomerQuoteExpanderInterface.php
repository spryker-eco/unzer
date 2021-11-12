<?php

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\QuoteTransfer;

interface UnzerCustomerQuoteExpanderInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return QuoteTransfer
     */
    public function expandQuoteWithUnzerCustomer(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
