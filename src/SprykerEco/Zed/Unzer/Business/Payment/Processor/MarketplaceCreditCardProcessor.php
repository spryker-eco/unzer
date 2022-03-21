<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;

class MarketplaceCreditCardProcessor extends CreditCardProcessor implements UnzerChargeablePaymentProcessorInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentResourceTransfer
     */
    protected function getUnzerPaymentResourceFromQuote(QuoteTransfer $quoteTransfer): UnzerPaymentResourceTransfer
    {
        return $quoteTransfer->getPaymentOrFail()->getUnzerMarketplaceCreditCardOrFail()->getPaymentResourceOrFail();
    }
}
