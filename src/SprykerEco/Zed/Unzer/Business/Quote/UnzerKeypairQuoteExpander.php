<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\Payment\KeypairResolver\UnzerKeypairResolverInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToStoreFacadeInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerKeypairQuoteExpander implements UnzerKeypairQuoteExpanderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\KeypairResolver\UnzerKeypairResolverInterface
     */
    protected $unzerKeypairResolver;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToStoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Payment\KeypairResolver\UnzerKeypairResolverInterface $unzerKeypairResolver
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToStoreFacadeInterface $storeFacade
     */
    public function __construct(
        UnzerKeypairResolverInterface $unzerKeypairResolver,
        UnzerConfig $unzerConfig,
        UnzerToStoreFacadeInterface $storeFacade
    ) {
        $this->unzerKeypairResolver = $unzerKeypairResolver;
        $this->unzerConfig = $unzerConfig;
        $this->storeFacade = $storeFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
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

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
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
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param string $merchantReference
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function setMerchantUnzerKeypair(UnzerPaymentTransfer $unzerPaymentTransfer, string $merchantReference): UnzerPaymentTransfer
    {
        $unzerKeypairTransfer = $this->unzerKeypairResolver->getUnzerKeypairByMerchantReferenceAndStore(
            $merchantReference,
            $this->storeFacade->getCurrentStore(),
        );

        return $unzerPaymentTransfer->setUnzerKeypair($unzerKeypairTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function setPrimaryUnzerKeypair(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        $unzerKeypairTransfer = $this->unzerKeypairResolver->getUnzerKeypairByKeypairId($this->unzerConfig->getUnzerPrimaryKeypairId());

        return $unzerPaymentTransfer->setUnzerKeypair($unzerKeypairTransfer);
    }
}
