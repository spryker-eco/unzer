<?php

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\Payment\KeypairResolver\UnzerKeypairResolverInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToStoreFacadeInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerKeypairQuoteExpander implements UnzerKeypairQuoteExpanderInterface
{
    /**
     * @var UnzerKeypairResolverInterface
     */
    protected $unzerKeypairResolver;

    /**
     * @var UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var UnzerToStoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @param UnzerKeypairResolverInterface $unzerKeypairResolver
     * @param UnzerConfig $unzerConfig
     * @param UnzerToStoreFacadeInterface $storeFacade
     */
    public function __construct(
        UnzerKeypairResolverInterface $unzerKeypairResolver,
        UnzerConfig $unzerConfig,
        UnzerToStoreFacadeInterface $storeFacade
    )
    {
        $this->unzerKeypairResolver = $unzerKeypairResolver;
        $this->unzerConfig = $unzerConfig;
        $this->storeFacade = $storeFacade;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return QuoteTransfer
     */
    public function expandQuoteWithUnzerKeypair(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $unzerPaymentTransfer = $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail();

        $uniqueMerchantReferences = $this->extractUniqueMerchantReferences($quoteTransfer);
        if (!$unzerPaymentTransfer->getIsMarketplace() && count($uniqueMerchantReferences) === 1) {
            $unzerPaymentTransfer = $this->setMerchantUnzerKeypair($unzerPaymentTransfer, $uniqueMerchantReferences[0]);
        } else {
            $unzerPaymentTransfer = $this->setPrimaryUnzerKeypair($unzerPaymentTransfer);
        }

        $quoteTransfer->getPaymentOrFail()->setUnzerPayment($unzerPaymentTransfer);

        return $quoteTransfer;
    }

    protected function extractUniqueMerchantReferences(QuoteTransfer $quoteTransfer): array
    {
        $uniqueMerchantReferences = [];
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $merchantReference = $itemTransfer->getMerchantReference();
            if ($merchantReference !== null && !in_array($merchantReference, $uniqueMerchantReferences, true)) {
                $uniqueMerchantReferences[] = $merchantReference;
            }
        }

        return $uniqueMerchantReferences;
    }

    /**
     * @param UnzerPaymentTransfer $unzerPaymentTransfer
     * @param string $merchantReference
     *
     * @return UnzerPaymentTransfer
     */
    protected function setMerchantUnzerKeypair(UnzerPaymentTransfer $unzerPaymentTransfer, string $merchantReference): UnzerPaymentTransfer
    {
        $unzerKeypairTransfer = $this->unzerKeypairResolver->getUnzerKeypairByMerchantReferenceAndStore(
            $merchantReference, $this->storeFacade->getCurrentStore()
        );

        return $unzerPaymentTransfer->setUnzerKeypair($unzerKeypairTransfer);
    }

    /**
     * @param UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return UnzerPaymentTransfer
     */
    protected function setPrimaryUnzerKeypair(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        $unzerKeypairTransfer = $this->unzerKeypairResolver->getUnzerKeypairByKeypairId($this->unzerConfig->getUnzerPrimaryKeypairId());

        return $unzerPaymentTransfer->setUnzerKeypair($unzerKeypairTransfer);
    }
}
