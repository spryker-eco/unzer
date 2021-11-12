<?php

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\QuoteTransfer;

interface UnzerMetadataQuoteExpanderInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return QuoteTransfer
     */
    public function expandQuoteWithUnzerMetadata(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
