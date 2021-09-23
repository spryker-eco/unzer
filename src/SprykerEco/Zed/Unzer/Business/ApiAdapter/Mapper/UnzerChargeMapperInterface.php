<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiChargeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiChargeResponseTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerChargeMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\UnzerApiChargeRequestTransfer $unzerApiChargeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiChargeRequestTransfer
     */
    public function mapUnzerPaymentTransferToUnzerApiChargeRequestTransfer(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        UnzerApiChargeRequestTransfer $unzerApiChargeRequestTransfer
    ): UnzerApiChargeRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapAuthorizableUnzerApiChargeResponseTransferToUnzerPaymentTransfer(
        UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapUnzerApiChargeResponseTransferToUnzerPaymentTransfer(
        UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer;
}
