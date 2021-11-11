<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantConditionsTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantCriteriaTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as SharedUnzerConfig;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteMapperInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToQuoteClientInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerQuoteExpander implements UnzerQuoteExpanderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface
     */
    protected $unzerCustomerAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteMapperInterface
     */
    protected $unzerQuoteExpanderMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToQuoteClientInterface
     */
    protected $quoteClient;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface $unzerCustomerAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteMapperInterface $unzerQuoteExpanderMapper
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToQuoteClientInterface $quoteClient
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     */
    public function __construct(
        UnzerCustomerAdapterInterface $unzerCustomerAdapter,
        UnzerQuoteMapperInterface $unzerQuoteExpanderMapper,
        UnzerToQuoteClientInterface $quoteClient,
        UnzerConfig $unzerConfig,
        UnzerReaderInterface $unzerReader
    ) {
        $this->unzerCustomerAdapter = $unzerCustomerAdapter;
        $this->unzerQuoteExpanderMapper = $unzerQuoteExpanderMapper;
        $this->quoteClient = $quoteClient;
        $this->unzerConfig = $unzerConfig;
        $this->unzerReader = $unzerReader;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expand(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        if ($quoteTransfer->getPaymentOrFail()->getPaymentProvider() !== SharedUnzerConfig::PROVIDER_NAME) {
            return $quoteTransfer;
        }

        $quoteTransfer = $this->addUnzerPayment($quoteTransfer);
        $quoteTransfer = $this->addUnzerCustomer($quoteTransfer);

        if ($quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getIsMarketplace()) {
            $quoteTransfer = $this->expandQuoteItemsWithUnzerParticipants($quoteTransfer);
        }

        $this->quoteClient->setQuote($quoteTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function addUnzerCustomer(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        if (
            $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail() !== null &&
            $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getCustomer() !== null
        ) {
            return $quoteTransfer;
        }

        $unzerCustomerTransfer = $this
            ->unzerQuoteExpanderMapper
            ->mapQuoteTransferToUnzerCustomerTransfer($quoteTransfer, new UnzerCustomerTransfer());

        $unzerCustomerTransfer = $this->unzerCustomerAdapter->createCustomer($unzerCustomerTransfer);
        $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->setCustomer($unzerCustomerTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function addUnzerPayment(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $paymentResourceTransfer = $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getPaymentResource();
        $paymentSelection = $quoteTransfer->getPaymentOrFail()->getPaymentSelection();

        $unzerPaymentTransfer = (new UnzerPaymentTransfer())
            ->setIsMarketplace($this->unzerConfig->isPaymentMethodMarketplaceReady($paymentSelection))
            ->setIsAuthorizable($this->unzerConfig->isPaymentAuthorizeRequired($paymentSelection))
            ->setPaymentResource($paymentResourceTransfer);

        $quoteTransfer->getPaymentOrFail()->setUnzerPayment($unzerPaymentTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function expandQuoteItemsWithUnzerParticipants(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $this->setParticipantId($itemTransfer);
        }

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function setParticipantId(ItemTransfer $itemTransfer): ItemTransfer
    {
        if (!$itemTransfer->getMerchantReference()) {
            return $itemTransfer;
        }

        $merchantUnzerParticipantCriteriaTransfer = (new MerchantUnzerParticipantCriteriaTransfer())
            ->setMerchantUnzerParticipantConditions(
                (new MerchantUnzerParticipantConditionsTransfer())->setReferences([$itemTransfer->getMerchantReference()]),
            );

        $merchantUnzerParticipantTransfer = $this->unzerReader->getMerchantUnzerParticipantByCriteria($merchantUnzerParticipantCriteriaTransfer);

        if ($merchantUnzerParticipantTransfer === null) {
            return $itemTransfer;
        }

        return $itemTransfer->setUnzerParticipantId($merchantUnzerParticipantTransfer->getParticipantId());
    }
}
