<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;

class UnzerParticipantIdQuoteExpander implements UnzerParticipantIdQuoteExpanderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     */
    public function __construct(UnzerReaderInterface $unzerReader)
    {
        $this->unzerReader = $unzerReader;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expandQuoteItemsWithParticipantIds(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $unzerCredentialsTransfer = $this->getMarketplaceMainUnzerCredentials($quoteTransfer->getStoreOrFail());
        if ($unzerCredentialsTransfer === null) {
            return $quoteTransfer;
        }

        $mainMerchantParticipantId = $this->getMainMerchantParticipantId($unzerCredentialsTransfer);
        $merchantUnzerCredentialsCollection = $this->getMerchantsUnzerCredentials($unzerCredentialsTransfer);

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $this->addParticipantIdToItemTransfer($itemTransfer, $merchantUnzerCredentialsCollection, $mainMerchantParticipantId);
        }

        foreach ($quoteTransfer->getExpenses() as $expenseTransfer) {
            $this->addParticipantIdToExpenseTransfer($expenseTransfer, $merchantUnzerCredentialsCollection, $mainMerchantParticipantId);
        }

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer $unzerCredentialsCollectionTransfer
     * @param string $mainMerchantParticipantId
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function addParticipantIdToItemTransfer(
        ItemTransfer $itemTransfer,
        UnzerCredentialsCollectionTransfer $unzerCredentialsCollectionTransfer,
        string $mainMerchantParticipantId
    ): ItemTransfer {
        if (!$itemTransfer->getMerchantReference()) {
            return $itemTransfer->setUnzerParticipantId($mainMerchantParticipantId);
        }

        return $itemTransfer->setUnzerParticipantId(
            $this->extractMerchantParticipantIdByMerchantReference($unzerCredentialsCollectionTransfer, $itemTransfer->getMerchantReference()),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @param \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer $unzerCredentialsCollectionTransfer
     * @param string $mainMerchantParticipantId
     *
     * @return \Generated\Shared\Transfer\ExpenseTransfer
     */
    protected function addParticipantIdToExpenseTransfer(
        ExpenseTransfer $expenseTransfer,
        UnzerCredentialsCollectionTransfer $unzerCredentialsCollectionTransfer,
        string $mainMerchantParticipantId
    ): ExpenseTransfer {
        $merchantReference = $expenseTransfer->getShipmentOrFail()->getMerchantReference();

        if ($merchantReference === null) {
            return $expenseTransfer->setUnzerParticipantId($mainMerchantParticipantId);
        }

        return $expenseTransfer->setUnzerParticipantId(
            $this->extractMerchantParticipantIdByMerchantReference($unzerCredentialsCollectionTransfer, $merchantReference),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer $unzerCredentialsCollectionTransfer
     * @param string $merchantReference
     *
     * @return string|null
     */
    protected function extractMerchantParticipantIdByMerchantReference(
        UnzerCredentialsCollectionTransfer $unzerCredentialsCollectionTransfer,
        string $merchantReference
    ): ?string {
        foreach ($unzerCredentialsCollectionTransfer->getUnzerCredentials() as $unzerCredentialsTransfer) {
            if ($unzerCredentialsTransfer->getMerchantReference() === $merchantReference) {
                return $unzerCredentialsTransfer->getParticipantIdOrFail();
            }
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer|null
     */
    protected function getMarketplaceMainUnzerCredentials(StoreTransfer $storeTransfer): ?UnzerCredentialsTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addStoreName($storeTransfer->getNameOrFail())
                    ->addType(UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE),
            );

        return $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $marketplaceMainUnzerCredentials
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return string
     */
    protected function getMainMerchantParticipantId(UnzerCredentialsTransfer $marketplaceMainUnzerCredentials): string
    {
        $parentIdUnzerCredentials = $marketplaceMainUnzerCredentials->getIdUnzerCredentialsOrFail();
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addParentId($parentIdUnzerCredentials)
                    ->addType(UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT),
            );

        $unzerCredentialsTransfer = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsTransfer === null) {
            throw new UnzerException(
                sprintf('Participant Id was not found for Main merchant with parent credentials id %s', $parentIdUnzerCredentials),
            );
        }

        return $unzerCredentialsTransfer->getParticipantIdOrFail();
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $marketplaceMainUnzerCredentials
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer
     */
    protected function getMerchantsUnzerCredentials(UnzerCredentialsTransfer $marketplaceMainUnzerCredentials): UnzerCredentialsCollectionTransfer
    {
        $parentIdUnzerCredentials = $marketplaceMainUnzerCredentials->getIdUnzerCredentialsOrFail();
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addParentId($parentIdUnzerCredentials)
                    ->setTypes([
                        UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT,
                        UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT,
                    ]),
            );

        return $this->unzerReader->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);
    }
}
