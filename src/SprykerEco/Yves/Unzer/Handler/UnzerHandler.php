<?php

namespace SprykerEco\Yves\Unzer\Handler;

use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;
use Symfony\Component\HttpFoundation\Request;

class UnzerHandler implements UnzerHandlerInterface
{
    /**
     * @param Request $request
     * @param QuoteTransfer $quoteTransfer
     *
     * @return QuoteTransfer
     */
    public function addPaymentToQuote(Request $request, QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $paymentSelection = $quoteTransfer->getPayment()->getPaymentSelection();

        $quoteTransfer->getPayment()
            ->setPaymentProvider(UnzerConfig::PAYMENT_PROVIDER_NAME)
            ->setPaymentMethod($paymentSelection);

        return $quoteTransfer;
    }
}
