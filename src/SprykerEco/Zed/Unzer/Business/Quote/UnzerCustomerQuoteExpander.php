<?php

namespace SprykerEco\Zed\Unzer\Business\Quote;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerCustomerMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteMapperInterface;

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
     * @param UnzerCustomerAdapterInterface $unzerCustomerAdapter
     * @param UnzerCustomerMapperInterface $unzerCustomerMapper
     * @param UnzerQuoteMapperInterface $unzerQuoteMapper
     */
    public function __construct(
        UnzerCustomerAdapterInterface $unzerCustomerAdapter,
        UnzerCustomerMapperInterface $unzerCustomerMapper,
        UnzerQuoteMapperInterface $unzerQuoteMapper
    )
    {
        $this->unzerCustomerAdapter = $unzerCustomerAdapter;
        $this->unzerCustomerMapper = $unzerCustomerMapper;
        $this->unzerQuoteMapper = $unzerQuoteMapper;
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

        $unzerCustomerTransfer = $this->unzerQuoteMapper
            ->mapQuoteTransferToUnzerCustomerTransfer($quoteTransfer, new UnzerCustomerTransfer());

        $unzerCustomerTransfer = $this->unzerCustomerAdapter->createCustomer($unzerCustomerTransfer);
        $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->setCustomer($unzerCustomerTransfer);

        return $quoteTransfer;
    }
}
