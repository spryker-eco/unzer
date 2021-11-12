<?php

namespace SprykerEco\Yves\Unzer\Dependency;

use Generated\Shared\Transfer\QuoteTransfer;

interface UnzerToQuoteClientInterface
{
    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getQuote(): QuoteTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function setQuote(QuoteTransfer $quoteTransfer);
}
