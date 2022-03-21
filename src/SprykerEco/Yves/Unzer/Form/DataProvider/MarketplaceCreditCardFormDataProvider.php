<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Form\DataProvider;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Yves\Unzer\Form\MarketplaceCreditCardSubForm;

class MarketplaceCreditCardFormDataProvider extends AbstractFormDataProvider
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array<string, mixed>
     */
    public function getOptions(AbstractTransfer $quoteTransfer): array
    {
        return [
            MarketplaceCreditCardSubForm::OPTION_PUBLIC_KEY => $quoteTransfer->getUnzerCredentialsOrFail()->getUnzerKeypairOrFail()->getPublicKeyOrFail(),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getData(AbstractTransfer $quoteTransfer): QuoteTransfer
    {
        $quoteTransfer = $this->updateQuoteWithPaymentData($quoteTransfer);
        $quoteTransfer->getPaymentOrFail()->setUnzerMarketplaceCreditCard(
            (new UnzerPaymentTransfer())->setPaymentResource(
                (new UnzerPaymentResourceTransfer())
            ),
        );

        $this->quoteClient->setQuote($quoteTransfer);

        return $quoteTransfer;
    }
}
