<?php

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerPreparePaymentProcessorInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function prepareUnzerPaymentTransfer(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): UnzerPaymentTransfer;
}
