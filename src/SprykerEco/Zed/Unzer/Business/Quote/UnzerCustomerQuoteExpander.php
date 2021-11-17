<?php

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
     * @var UnzerCustomerAdapterInterface
     */
    protected $unzerCustomerAdapter;

    /**
     * @var UnzerCustomerMapperInterface
     */
    protected $unzerCustomerMapper;

    /**
     * @var UnzerQuoteMapperInterface
     */
    protected $unzerQuoteMapper;

    /**
     * @var UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var UnzerWriterInterface
     */
    protected $unzerWriter;

    /**
     * @param UnzerCustomerAdapterInterface $unzerCustomerAdapter
     * @param UnzerCustomerMapperInterface $unzerCustomerMapper
     * @param UnzerQuoteMapperInterface $unzerQuoteMapper
     * @param UnzerReaderInterface $unzerReader
     * @param UnzerWriterInterface $unzerWriter
     */
    public function __construct(
        UnzerCustomerAdapterInterface $unzerCustomerAdapter,
        UnzerCustomerMapperInterface $unzerCustomerMapper,
        UnzerQuoteMapperInterface $unzerQuoteMapper,
        UnzerReaderInterface $unzerReader,
        UnzerWriterInterface $unzerWriter
    )
    {
        $this->unzerCustomerAdapter = $unzerCustomerAdapter;
        $this->unzerCustomerMapper = $unzerCustomerMapper;
        $this->unzerQuoteMapper = $unzerQuoteMapper;
        $this->unzerReader = $unzerReader;
        $this->unzerWriter = $unzerWriter;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return QuoteTransfer
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
     * @param QuoteTransfer $quoteTransfer
     *
     * @return UnzerCustomerTransfer
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
     * @param QuoteTransfer $quoteTransfer
     *
     * @return UnzerCustomerTransfer
     */
    protected function createUnzerCustomer(QuoteTransfer $quoteTransfer): UnzerCustomerTransfer
    {
        $unzerCustomerTransfer = $this->unzerQuoteMapper
            ->mapQuoteTransferToUnzerCustomerTransfer($quoteTransfer, new UnzerCustomerTransfer());

        return $this->unzerCustomerAdapter->createCustomer($unzerCustomerTransfer);
    }

    /**
     * @param UnzerCustomerTransfer $unzerCustomerTransfer
     * @param QuoteTransfer $quoteTransfer
     *
     * @return UnzerCustomerTransfer
     */
    protected function updateUnzerCustomer(UnzerCustomerTransfer $unzerCustomerTransfer, QuoteTransfer $quoteTransfer): UnzerCustomerTransfer
    {
        $unzerCustomerTransfer = $this->unzerQuoteMapper
            ->mapQuoteTransferToUnzerCustomerTransfer($quoteTransfer, $unzerCustomerTransfer);

        return $this->unzerCustomerAdapter->updateCustomer($unzerCustomerTransfer);
    }
}
