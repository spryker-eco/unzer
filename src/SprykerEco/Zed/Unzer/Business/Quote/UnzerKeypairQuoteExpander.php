<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface;

class UnzerKeypairQuoteExpander implements UnzerKeypairQuoteExpanderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface
     */
    protected $unzerCredentialsResolver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface $unzerCredentialsResolver
     */
    public function __construct(UnzerCredentialsResolverInterface $unzerCredentialsResolver)
    {
        $this->unzerCredentialsResolver = $unzerCredentialsResolver;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expandQuoteWithUnzerKeypair(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $uniqueMerchantReferences = $this->extractUniqueMerchantReferences($quoteTransfer);

        if ($quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getIsMarketplaceOrFail()) {
            return $this->setMainMarketplaceUnzerKeypair($quoteTransfer);
        }

        if (count($uniqueMerchantReferences) === 1) {
            return $this->setMarketplaceMerchantUnzerKeypair($quoteTransfer, $uniqueMerchantReferences[0]);
        }

        return $this->setRegularUnzerKeypair($quoteTransfer);
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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function setRegularUnzerKeypair(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addType(UnzerConstants::UNZER_CONFIG_TYPE_STANDARD)
                    ->addStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail()),
            );

        $unzerCredentialsTransfer = $this->unzerCredentialsResolver->resolveUnzerCredentialsByCriteriaTransfer($unzerCredentialsCriteriaTransfer);
        $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->setUnzerKeypair($unzerCredentialsTransfer->getUnzerKeypairOrFail());

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function setMainMarketplaceUnzerKeypair(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addType(UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE)
                    ->addStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail()),
            );

        $unzerCredentialsTransfer = $this->unzerCredentialsResolver->resolveUnzerCredentialsByCriteriaTransfer($unzerCredentialsCriteriaTransfer);
        $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->setUnzerKeypair($unzerCredentialsTransfer->getUnzerKeypairOrFail());

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $merchantReference
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function setMarketplaceMerchantUnzerKeypair(QuoteTransfer $quoteTransfer, string $merchantReference): QuoteTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addType(UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT)
                    ->addStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail())
                    ->addMerchantReference($merchantReference),
            );

        $unzerCredentialsTransfer = $this->unzerCredentialsResolver->resolveUnzerCredentialsByCriteriaTransfer($unzerCredentialsCriteriaTransfer);
        $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->setUnzerKeypair($unzerCredentialsTransfer->getUnzerKeypairOrFail());

        return $quoteTransfer;
    }
}
