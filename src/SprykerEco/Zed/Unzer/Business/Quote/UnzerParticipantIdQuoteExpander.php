<?php

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;

class UnzerParticipantIdQuoteExpander implements UnzerParticipantIdQuoteExpanderInterface
{
    /**
     * @var UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @param UnzerReaderInterface $unzerReader
     */
    public function __construct(
        UnzerReaderInterface $unzerReader
    )
    {
        $this->unzerReader = $unzerReader;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return QuoteTransfer
     */
    public function expandQuoteItemsWithParticipantIds(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $marketplaceMainUnzerCredentials = $this->getMarketplaceMainUnzerCredentials($quoteTransfer->getStore());
        if ($marketplaceMainUnzerCredentials === null) {
            return $quoteTransfer;
        }

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $this->setParticipantId($itemTransfer, $marketplaceMainUnzerCredentials);
        }

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\StoreTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function setParticipantId(ItemTransfer $itemTransfer, UnzerCredentialsTransfer $unzerCredentialsTransfer): ItemTransfer
    {
        if (!$itemTransfer->getMerchantReference()) {
            return $itemTransfer->setUnzerParticipantId(
                $this->getMainMerchantParticipantId($unzerCredentialsTransfer->getIdUnzerCredentials())
                );
        }

        return $itemTransfer->setUnzerParticipantId(
            $this->getMerchantParticipantId($unzerCredentialsTransfer->getIdUnzerCredentials(), $itemTransfer->getMerchantReference())
        );
    }

    /**
     * @param int $parentIdUnzerCredentials
     * @param string $merchantReference
     *
     * @return string|null
     */
    protected function getMerchantParticipantId(int $parentIdUnzerCredentials, string $merchantReference): ?string
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addParentId($parentIdUnzerCredentials)
                    ->addMerchantReference($merchantReference)
                    ->addType(UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT),
            );

        $unzerCredentialsTransfer = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsTransfer === null) {
            return null;
        }

        return $unzerCredentialsTransfer->getParticipantId();
    }

    /**
     * @param int $parentIdUnzerCredentials
     *
     * @return string|null
     */
    protected function getMainMerchantParticipantId(int $parentIdUnzerCredentials): ?string
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addParentId($parentIdUnzerCredentials)
                    ->addType(UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT),
            );

        $unzerCredentialsTransfer = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsTransfer === null) {
            return null;
        }

        return $unzerCredentialsTransfer->getParticipantId();
    }

    /**
     * @param StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer|null
     */
    protected function getMarketplaceMainUnzerCredentials(StoreTransfer $storeTransfer): ?UnzerCredentialsTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addStoreName($storeTransfer->getName())
                    ->addType(UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE),
            );

        return $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
    }
}
