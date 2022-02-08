<?php

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\QuoteTransfer;

interface UnzerParticipantIdQuoteExpanderInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return QuoteTransfer
     */
    public function expandQuoteItemsWithParticipantIds(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
