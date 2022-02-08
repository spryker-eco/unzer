<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as SharedUnzerConfig;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToQuoteClientInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerQuoteExpander implements UnzerQuoteExpanderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Quote\UnzerCustomerQuoteExpanderInterface
     */
    protected $unzerCustomerQuoteExpander;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Quote\UnzerMetadataQuoteExpanderInterface
     */
    protected $unzerMetadataQuoteExpander;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Quote\UnzerKeypairQuoteExpanderInterface
     */
    protected $unzerKeypairQuoteExpander;

    /**
     * @var UnzerParticipantIdQuoteExpanderInterface
     */
    protected $unzerParticipantIdQuoteExpander;

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
     * @param \SprykerEco\Zed\Unzer\Business\Quote\UnzerCustomerQuoteExpanderInterface $unzerCustomerQuoteExpander
     * @param \SprykerEco\Zed\Unzer\Business\Quote\UnzerMetadataQuoteExpanderInterface $unzerMetadataQuoteExpander
     * @param \SprykerEco\Zed\Unzer\Business\Quote\UnzerKeypairQuoteExpanderInterface $unzerKeypairQuoteExpander
     * @param UnzerParticipantIdQuoteExpanderInterface $unzerParticipantIdQuoteExpander
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToQuoteClientInterface $quoteClient
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(
        UnzerCustomerQuoteExpanderInterface $unzerCustomerQuoteExpander,
        UnzerMetadataQuoteExpanderInterface $unzerMetadataQuoteExpander,
        UnzerKeypairQuoteExpanderInterface $unzerKeypairQuoteExpander,
        UnzerParticipantIdQuoteExpanderInterface $unzerParticipantIdQuoteExpander,
        UnzerToQuoteClientInterface $quoteClient,
        UnzerConfig $unzerConfig
    ) {
        $this->unzerCustomerQuoteExpander = $unzerCustomerQuoteExpander;
        $this->unzerMetadataQuoteExpander = $unzerMetadataQuoteExpander;
        $this->unzerKeypairQuoteExpander = $unzerKeypairQuoteExpander;
        $this->unzerParticipantIdQuoteExpander = $unzerParticipantIdQuoteExpander;
        $this->quoteClient = $quoteClient;
        $this->unzerConfig = $unzerConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expand(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        if ($quoteTransfer->getPaymentOrFail()->getPaymentProvider() !== SharedUnzerConfig::PAYMENT_PROVIDER_NAME) {
            return $quoteTransfer;
        }

        $quoteTransfer = $this->addUnzerPayment($quoteTransfer);
        $quoteTransfer = $this->unzerKeypairQuoteExpander->expandQuoteWithUnzerKeypair($quoteTransfer);
        $quoteTransfer = $this->unzerCustomerQuoteExpander->expandQuoteWithUnzerCustomer($quoteTransfer);
        $quoteTransfer = $this->unzerMetadataQuoteExpander->expandQuoteWithUnzerMetadata($quoteTransfer);

        if ($quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getIsMarketplace()) {
            $quoteTransfer = $this->unzerParticipantIdQuoteExpander->expandQuoteItemsWithParticipantIds($quoteTransfer);
        }

        $this->quoteClient->setQuote($quoteTransfer);

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
}
