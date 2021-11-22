<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerCustomerMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteMapperInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface;

class UnzerCustomerQuoteExpander implements UnzerCustomerQuoteExpanderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface
     */
    protected $unzerCustomerAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerCustomerMapperInterface
     */
    protected $unzerCustomerMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteMapperInterface
     */
    protected $unzerQuoteMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface
     */
    protected $unzerWriter;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface $unzerCustomerAdapter
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerCustomerMapperInterface $unzerCustomerMapper
     * @param \SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteMapperInterface $unzerQuoteMapper
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface $unzerWriter
     */
    public function __construct(
        UnzerCustomerAdapterInterface $unzerCustomerAdapter,
        UnzerCustomerMapperInterface $unzerCustomerMapper,
        UnzerQuoteMapperInterface $unzerQuoteMapper,
        UnzerReaderInterface $unzerReader,
        UnzerWriterInterface $unzerWriter
    ) {
        $this->unzerCustomerAdapter = $unzerCustomerAdapter;
        $this->unzerCustomerMapper = $unzerCustomerMapper;
        $this->unzerQuoteMapper = $unzerQuoteMapper;
        $this->unzerReader = $unzerReader;
        $this->unzerWriter = $unzerWriter;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expandQuoteWithUnzerCustomer(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        if (
            $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail() !== null &&
            $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getCustomer() !== null
        ) {
            return $quoteTransfer;
        }

        $unzerCustomerTransfer = $this->retrieveUnzerCustomerTransfer($quoteTransfer);
        $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->setCustomer($unzerCustomerTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    protected function retrieveUnzerCustomerTransfer(QuoteTransfer $quoteTransfer): UnzerCustomerTransfer
    {
        if ($quoteTransfer->getCustomerOrFail()->getIsGuest()) {
            return $this->createUnzerCustomer($quoteTransfer);
        }

        $unzerCustomerTransfer = $this->unzerReader->getUnzerCustomerTransferByCustomerTransfer($quoteTransfer->getCustomer());
        if ($unzerCustomerTransfer !== null) {
            return $this->updateUnzerCustomer($unzerCustomerTransfer, $quoteTransfer);
        }

        return $this->createUnzerCustomer($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    protected function createUnzerCustomer(QuoteTransfer $quoteTransfer): UnzerCustomerTransfer
    {
        $unzerCustomerTransfer = $this->unzerQuoteMapper
            ->mapQuoteTransferToUnzerCustomerTransfer($quoteTransfer, new UnzerCustomerTransfer());

        return $this->unzerCustomerAdapter->createCustomer(
            $unzerCustomerTransfer,
            $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getUnzerKeypairOrFail(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    protected function updateUnzerCustomer(UnzerCustomerTransfer $unzerCustomerTransfer, QuoteTransfer $quoteTransfer): UnzerCustomerTransfer
    {
        $unzerCustomerTransfer = $this->unzerQuoteMapper
            ->mapQuoteTransferToUnzerCustomerTransfer($quoteTransfer, $unzerCustomerTransfer);

        return $this->unzerCustomerAdapter->updateCustomer(
            $unzerCustomerTransfer,
            $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getUnzerKeypairOrFail(),
        );
    }
}
