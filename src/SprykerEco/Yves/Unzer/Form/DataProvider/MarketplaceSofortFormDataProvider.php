<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Form\DataProvider;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Yves\Unzer\Dependency\Client\UnzerToQuoteClientInterface;

class MarketplaceSofortFormDataProvider extends AbstractFormDataProvider
{
    /**
     * @var \SprykerEco\Yves\Unzer\Dependency\Client\UnzerToQuoteClientInterface
     */
    protected $quoteClient;

    /**
     * @param \SprykerEco\Yves\Unzer\Dependency\Client\UnzerToQuoteClientInterface $quoteClient
     */
    public function __construct(UnzerToQuoteClientInterface $quoteClient)
    {
        $this->quoteClient = $quoteClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getData(AbstractTransfer $quoteTransfer): QuoteTransfer
    {
        $quoteTransfer = $this->updateQuoteWithPaymentData($quoteTransfer);
        $quoteTransfer->getPaymentOrFail()->setUnzerMarketplaceSofort(new UnzerPaymentTransfer());

        $this->quoteClient->setQuote($quoteTransfer);

        return $quoteTransfer;
    }
}
