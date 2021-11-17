<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Handler;

use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;
use Symfony\Component\HttpFoundation\Request;

class UnzerHandler implements UnzerHandlerInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
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
