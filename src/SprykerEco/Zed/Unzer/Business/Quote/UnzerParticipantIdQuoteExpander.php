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
     * @var string
     */
    protected $mainMerchantParticipantId;

    /**
     * @var \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer
     */
    protected $merchantUnzerCredentialsCollection;

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
        $marketplaceMainUnzerCredentials = $this->getMarketplaceMainUnzerCredentials($quoteTransfer->getStoreOrFail());
        if ($marketplaceMainUnzerCredentials === null) {
            return $quoteTransfer;
        }

        $this->setMainMerchantParticipantId($marketplaceMainUnzerCredentials);
        $this->setMerchantsUnzerCredentials($marketplaceMainUnzerCredentials);

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $this->addParticipantIdToItemTransfer($itemTransfer);
        }

        foreach ($quoteTransfer->getExpenses() as $expenseTransfer) {
            $this->addParticipantIdToExpenseTransfer($expenseTransfer);
        }

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function addParticipantIdToItemTransfer(ItemTransfer $itemTransfer): ItemTransfer
    {
        if (!$itemTransfer->getMerchantReference()) {
            return $itemTransfer->setUnzerParticipantId($this->mainMerchantParticipantId);
        }

        return $itemTransfer->setUnzerParticipantId(
            $this->getMerchantParticipantIdByMerchantReference($itemTransfer->getMerchantReference()),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return \Generated\Shared\Transfer\ExpenseTransfer
     */
    protected function addParticipantIdToExpenseTransfer(ExpenseTransfer $expenseTransfer): ExpenseTransfer
    {
        $merchantReference = $expenseTransfer->getShipmentOrFail()->getMerchantReference();

        if ($merchantReference === null) {
            return $expenseTransfer->setUnzerParticipantId($this->mainMerchantParticipantId);
        }

        return $expenseTransfer->setUnzerParticipantId(
            $this->getMerchantParticipantIdByMerchantReference($merchantReference),
        );
    }

    /**
     * @param string $merchantReference
     *
     * @return string|null
     */
    protected function getMerchantParticipantIdByMerchantReference(string $merchantReference): ?string
    {
        foreach ($this->merchantUnzerCredentialsCollection->getUnzerCredentials() as $unzerCredentialsTransfer) {
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
     * @return void
     */
    protected function setMainMerchantParticipantId(UnzerCredentialsTransfer $marketplaceMainUnzerCredentials): void
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

        $this->mainMerchantParticipantId = $unzerCredentialsTransfer->getParticipantIdOrFail();
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $marketplaceMainUnzerCredentials
     *
     * @return void
     */
    protected function setMerchantsUnzerCredentials(UnzerCredentialsTransfer $marketplaceMainUnzerCredentials): void
    {
        $parentIdUnzerCredentials = $marketplaceMainUnzerCredentials->getIdUnzerCredentialsOrFail();
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addParentId($parentIdUnzerCredentials)
                    ->addType(UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT),
            );

        $this->merchantUnzerCredentialsCollection = $this->unzerReader->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);
    }
}
