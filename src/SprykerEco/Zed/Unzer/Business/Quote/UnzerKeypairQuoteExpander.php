<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface;

class UnzerKeypairQuoteExpander implements UnzerKeypairQuoteExpanderInterface
{
    /**
     * @var string
     */
    protected const MAIN_SELLER_REFERENCE = 'main';

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
        if ($quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getIsMarketplaceOrFail()) {
            return $this->setMainMarketplaceUnzerKeypair($quoteTransfer);
        }

        $uniqueMerchantReferences = $this->extractUniqueMerchantReferences($quoteTransfer);
        if (count($uniqueMerchantReferences) === 1 && $uniqueMerchantReferences[0] !== static::MAIN_SELLER_REFERENCE) {
            return $this->setMarketplaceMerchantUnzerKeypair($quoteTransfer, $uniqueMerchantReferences[0]);
        }

        return $this->setRegularUnzerKeypair($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expandQuoteWithUnzerCredentials(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        if ($quoteTransfer->getItems()->count() === 0) {
            return $quoteTransfer;
        }

        $uniqueMerchantReferences = $this->extractUniqueMerchantReferences($quoteTransfer);
        if (count($uniqueMerchantReferences) > 1) {
            $unzerCredentialsTransfer = $this->getMainMarketplaceUnzerCredentialsTransfer($quoteTransfer);

            return $quoteTransfer->setUnzerCredentials($unzerCredentialsTransfer);
        }

        if (count($uniqueMerchantReferences) === 1 && $uniqueMerchantReferences[0] !== static::MAIN_SELLER_REFERENCE) {
            $unzerCredentialsTransfer = $this->getMarketplaceMerchantUnzerCredentialsTransfer($quoteTransfer, $uniqueMerchantReferences[0]);

            return $quoteTransfer->setUnzerCredentials($unzerCredentialsTransfer);
        }

        $unzerCredentialsTransfer = $this->getRegularUnzerCredentials($quoteTransfer);

        return $quoteTransfer->setUnzerCredentials($unzerCredentialsTransfer);
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
            $merchantReference = $itemTransfer->getMerchantReference() ?? static::MAIN_SELLER_REFERENCE;
            if (!in_array($merchantReference, $uniqueMerchantReferences, true)) {
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
        $unzerCredentialsTransfer = $this->getRegularUnzerCredentials($quoteTransfer);

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
        $unzerCredentialsTransfer = $this->getMainMarketplaceUnzerCredentialsTransfer($quoteTransfer);

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
        $unzerCredentialsTransfer = $this->getMarketplaceMerchantUnzerCredentialsTransfer($quoteTransfer, $merchantReference);

        $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->setUnzerKeypair($unzerCredentialsTransfer->getUnzerKeypairOrFail());

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    protected function getRegularUnzerCredentials(QuoteTransfer $quoteTransfer): UnzerCredentialsTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addType(UnzerConstants::UNZER_CONFIG_TYPE_STANDARD)
                    ->addStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail()),
            );

        return $this->unzerCredentialsResolver->resolveUnzerCredentialsByCriteriaTransfer($unzerCredentialsCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $merchantReference
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    protected function getMarketplaceMerchantUnzerCredentialsTransfer(QuoteTransfer $quoteTransfer, string $merchantReference): UnzerCredentialsTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->setTypes([
                        UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT,
                        UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT,
                    ])
                    ->addStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail())
                    ->addMerchantReference($merchantReference),
            );

        return $this->unzerCredentialsResolver->resolveUnzerCredentialsByCriteriaTransfer($unzerCredentialsCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    protected function getMainMarketplaceUnzerCredentialsTransfer(QuoteTransfer $quoteTransfer): UnzerCredentialsTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addType(UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE)
                    ->addStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail()),
            );

        return $this->unzerCredentialsResolver->resolveUnzerCredentialsByCriteriaTransfer($unzerCredentialsCriteriaTransfer);
    }
}
