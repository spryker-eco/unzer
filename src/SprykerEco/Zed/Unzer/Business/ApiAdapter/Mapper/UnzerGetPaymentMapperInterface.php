<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiGetPaymentRequestTransfer;
use Generated\Shared\Transfer\UnzerApiGetPaymentResponseTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerGetPaymentMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\UnzerApiGetPaymentRequestTransfer $unzerApiGetPaymentRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiGetPaymentRequestTransfer
     */
    public function mapUnzerPaymentTransferToUnzerApiGetPaymentRequestTransfer(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        UnzerApiGetPaymentRequestTransfer $unzerApiGetPaymentRequestTransfer
    ): UnzerApiGetPaymentRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerApiGetPaymentResponseTransfer $unzerApiGetPaymentResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapUnzerApiGetPaymentResponseTransferToUnzerPaymentTransfer(
        UnzerApiGetPaymentResponseTransfer $unzerApiGetPaymentResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer;
}
