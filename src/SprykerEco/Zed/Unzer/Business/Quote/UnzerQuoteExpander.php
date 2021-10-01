<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as SharedUnzerConfig;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteExpanderMapperInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToQuoteClientInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerQuoteExpander implements UnzerQuoteExpanderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface
     */
    protected $customerAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteExpanderMapperInterface
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
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface $customerAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteExpanderMapperInterface $unzerQuoteExpanderMapper
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToQuoteClientInterface $quoteClient
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     */
    public function __construct(
        UnzerCustomerAdapterInterface $customerAdapter,
        UnzerQuoteExpanderMapperInterface $unzerQuoteExpanderMapper,
        UnzerToQuoteClientInterface $quoteClient,
        UnzerConfig $unzerConfig,
        UnzerReaderInterface $unzerReader
    ) {
        $this->customerAdapter = $customerAdapter;
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
        if ($quoteTransfer->getPayment()->getPaymentProvider() !== SharedUnzerConfig::PROVIDER_NAME) {
            return $quoteTransfer;
        }

        $quoteTransfer = $this->createUnzerPayment($quoteTransfer);
        $quoteTransfer = $this->createUnzerCustomer($quoteTransfer);
        if ($quoteTransfer->getPayment()->getUnzerPayment()->getIsMarketplace()) {
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
    protected function createUnzerCustomer(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        if (
            $quoteTransfer->getPayment()->getUnzerPayment() !== null &&
            $quoteTransfer->getPayment()->getUnzerPayment()->getCustomer() !== null
        ) {
            return $quoteTransfer;
        }

        $unzerCustomerTransfer = $this
            ->unzerQuoteExpanderMapper
            ->mapQuoteTransferToUnzerCustomerTransfer($quoteTransfer, new UnzerCustomerTransfer());

        $unzerCustomerTransfer = $this->customerAdapter->createCustomer($unzerCustomerTransfer);
        $quoteTransfer->getPayment()->getUnzerPayment()->setCustomer($unzerCustomerTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createUnzerPayment(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $paymentResourceTransfer = $this->extractPaymentResourceFromQuote($quoteTransfer);
        $paymentSelection = $quoteTransfer->getPayment()->getPaymentSelection();

        $unzerPaymentTransfer = (new UnzerPaymentTransfer())
            ->setIsMarketplace($this->unzerConfig->isPaymentMethodMarketplaceReady($paymentSelection))
            ->setIsAuthorizable($this->unzerConfig->isPaymentAuthorizeRequired($paymentSelection))
            ->setPaymentResource($paymentResourceTransfer);

        $quoteTransfer->getPayment()->setUnzerPayment($unzerPaymentTransfer);

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
        if (empty($itemTransfer->getMerchantReference())) {
            return $itemTransfer;
        }

        $merchantUnzerParticipant = $this->unzerReader->getMerchantUnzerByMerchantReference($itemTransfer->getMerchantReference());
        if ($merchantUnzerParticipant->getMerchantId() === null) {
            return $itemTransfer;
        }

        return $itemTransfer->setUnzerParticipantId($merchantUnzerParticipant->getParticipantId());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentResourceTransfer|null
     */
    protected function extractPaymentResourceFromQuote(QuoteTransfer $quoteTransfer): ?UnzerPaymentResourceTransfer
    {
        if ($quoteTransfer->getPayment()->getUnzerPayment()->getPaymentResource() !== null) {
            return $quoteTransfer->getPayment()->getUnzerPayment()->getPaymentResource();
        }

        return null;
    }
}
