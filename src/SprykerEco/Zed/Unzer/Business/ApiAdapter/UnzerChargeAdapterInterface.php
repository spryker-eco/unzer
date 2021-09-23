<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerChargeAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function chargePayment(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function chargeAuthorizablePayment(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer;
}
