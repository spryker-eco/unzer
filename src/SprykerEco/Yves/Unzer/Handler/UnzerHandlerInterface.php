<?php

namespace SprykerEco\Yves\Unzer\Handler;

use Generated\Shared\Transfer\QuoteTransfer;
use Symfony\Component\HttpFoundation\Request;

interface UnzerHandlerInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addPaymentToQuote(Request $request, QuoteTransfer $quoteTransfer): QuoteTransfer;
}
