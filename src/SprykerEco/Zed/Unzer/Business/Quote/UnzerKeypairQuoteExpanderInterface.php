<?php

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\QuoteTransfer;

interface UnzerKeypairQuoteExpanderInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expandQuoteWithUnzerKeypair(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
