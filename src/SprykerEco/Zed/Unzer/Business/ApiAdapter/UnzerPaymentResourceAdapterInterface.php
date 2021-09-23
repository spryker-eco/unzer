<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;

interface UnzerPaymentResourceAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentResourceTransfer
     */
    public function createPaymentResource(UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer): UnzerPaymentResourceTransfer;
}
