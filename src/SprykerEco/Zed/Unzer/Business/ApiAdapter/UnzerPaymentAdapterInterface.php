<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerPaymentAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function getPaymentInfo(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer;
}
