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
use Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\Payment\Resolver\UnzerMarketplacePaymentUnzerCredentialsResolverInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;

class UnzerKeypairQuoteExpander implements UnzerKeypairQuoteExpanderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected UnzerReaderInterface $unzerReader;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Resolver\UnzerMarketplacePaymentUnzerCredentialsResolverInterface
     */
    protected UnzerMarketplacePaymentUnzerCredentialsResolverInterface $unzerMarketplacePaymentUnzerCredentialsResolver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Resolver\UnzerMarketplacePaymentUnzerCredentialsResolverInterface $unzerMarketplacePaymentUnzerCredentialsResolver
     */
    public function __construct(
        UnzerReaderInterface $unzerReader,
        UnzerMarketplacePaymentUnzerCredentialsResolverInterface $unzerMarketplacePaymentUnzerCredentialsResolver
    ) {
        $this->unzerReader = $unzerReader;
        $this->unzerMarketplacePaymentUnzerCredentialsResolver = $unzerMarketplacePaymentUnzerCredentialsResolver;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expandQuoteWithUnzerKeypair(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $uniqueMerchantReferences = $this->extractUniqueMerchantReferences($quoteTransfer);
        if (count($uniqueMerchantReferences) > 1) {
            return $this->setMainMarketplaceUnzerKeypair($quoteTransfer);
        }

        if (count($uniqueMerchantReferences) === 1 && $uniqueMerchantReferences[0] !== UnzerConstants::MAIN_SELLER_REFERENCE) {
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

        if (count($uniqueMerchantReferences) === 1 && $uniqueMerchantReferences[0] !== UnzerConstants::MAIN_SELLER_REFERENCE) {
            $unzerCredentialsTransfer = $this->getMarketplaceMerchantUnzerCredentialsTransfer($quoteTransfer, $uniqueMerchantReferences[0]);

            return $quoteTransfer->setUnzerCredentials($unzerCredentialsTransfer);
        }

        $unzerCredentialsTransfer = $this->getRegularUnzerCredentials($quoteTransfer);

        return $quoteTransfer->setUnzerCredentials($unzerCredentialsTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array<string>
     */
    protected function extractUniqueMerchantReferences(QuoteTransfer $quoteTransfer): array
    {
        $uniqueMerchantReferences = [];
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $merchantReference = $itemTransfer->getMerchantReference() ?? UnzerConstants::MAIN_SELLER_REFERENCE;
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

        if ($unzerCredentialsTransfer->getType() !== UnzerConstants::UNZER_CREDENTIALS_TYPE_STANDARD) {
            $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer = (new UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer())
                ->setQuote($quoteTransfer)
                ->setPaymentMethodKey($quoteTransfer->getPaymentOrFail()->getPaymentMethod());
            $unzerCredentialsTransfer = $this->unzerMarketplacePaymentUnzerCredentialsResolver
                ->findMarketplacePaymentUnzerCredentials($unzerMarketplacePaymentCredentialsResolverCriteriaTransfer);
        }

        $quoteTransfer->getPaymentOrFail()
            ->getUnzerPaymentOrFail()
            ->setUnzerKeypair($unzerCredentialsTransfer->getUnzerKeypairOrFail());

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
        $unzerKeypairTransfer = $this->getMarketplaceMerchantUnzerCredentialsTransfer($quoteTransfer, $merchantReference)
            ->getUnzerKeypairOrFail();

        if ($quoteTransfer->getPaymentOrFail()->getPaymentMethod() !== null) {
            $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer = (new UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer())
                ->setQuote($quoteTransfer)
                ->setPaymentMethodKey($quoteTransfer->getPaymentOrFail()->getPaymentMethod());
            $unzerKeypairTransfer = $this->unzerMarketplacePaymentUnzerCredentialsResolver
                ->findMarketplacePaymentUnzerCredentials($unzerMarketplacePaymentCredentialsResolverCriteriaTransfer)
                ->getUnzerKeypairOrFail();
        }

        $quoteTransfer->getPaymentOrFail()
            ->getUnzerPaymentOrFail()
            ->setUnzerKeypair($unzerKeypairTransfer);

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
                    ->setTypes([
                        UnzerConstants::UNZER_CREDENTIALS_TYPE_STANDARD,
                        UnzerConstants::UNZER_CREDENTIALS_TYPE_MARKETPLACE_MAIN_MERCHANT,
                        ])
                    ->addStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail()),
            );

        $unzerCredentials = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);

        return $unzerCredentials ?? new UnzerCredentialsTransfer();
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
                        UnzerConstants::UNZER_CREDENTIALS_TYPE_MARKETPLACE_MERCHANT,
                        UnzerConstants::UNZER_CREDENTIALS_TYPE_MARKETPLACE_MAIN_MERCHANT,
                    ])
                    ->addStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail())
                    ->addMerchantReference($merchantReference),
            );

        if ($quoteTransfer->getPayment() !== null && strpos(UnzerConfig::PLATFORM_MARKETPLACE, $quoteTransfer->getPaymentOrFail()->getPaymentMethodOrFail()) !== false) {
            $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
                ->setUnzerCredentialsConditions(
                    (new UnzerCredentialsConditionsTransfer())
                        ->setTypes([
                            UnzerConstants::UNZER_CREDENTIALS_TYPE_MAIN_MARKETPLACE,
                        ])
                        ->addStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail()),
                );
        }

        $unzerCredentials = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);

        return $unzerCredentials ?? new UnzerCredentialsTransfer();
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
                    ->addType(UnzerConstants::UNZER_CREDENTIALS_TYPE_MAIN_MARKETPLACE)
                    ->addStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail()),
            );

        $unzerCredentials = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);

        return $unzerCredentials ?? new UnzerCredentialsTransfer();
    }
}
