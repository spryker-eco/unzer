<?php

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerMetadataTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerMetadataAdapterInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToLocaleFacadeInterface;

class UnzerMetadataQuoteExpander implements UnzerMetadataQuoteExpanderInterface
{
    /**
     * @var UnzerMetadataAdapterInterface
     */
    protected $unzerMetadataAdapter;

    /**
     * @var UnzerToLocaleFacadeInterface
     */
    protected  $localeFacade;

    public function __construct(
        UnzerMetadataAdapterInterface $unzerMetadataAdapter,
        UnzerToLocaleFacadeInterface $localeFacade
    )
    {
        $this->unzerMetadataAdapter = $unzerMetadataAdapter;
        $this->localeFacade = $localeFacade;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return QuoteTransfer
     */
    public function expandQuoteWithUnzerMetadata(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $unzerMetadataTransfer = (new UnzerMetadataTransfer())
            ->setPriceMode($quoteTransfer->getPriceMode())
            ->setStore($quoteTransfer->getStoreOrFail()->getName())
            ->setLocale($this->localeFacade->getCurrentLocale()->getName())
            ->setCreatedAt(time());

        $unzerMetadataTransfer = $this->unzerMetadataAdapter->createMetadata($unzerMetadataTransfer);
        $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->setMetadata($unzerMetadataTransfer);

        return $quoteTransfer;
    }
}
