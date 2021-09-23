<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeResponseTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerAuthorizePaymentMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer $unzerApiMarketplaceAuthorizeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer
     */
    public function mapUnzerPaymentTransferToUnzerApiMarketplaceAuthorizeRequestTransfer(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        UnzerApiMarketplaceAuthorizeRequestTransfer $unzerApiMarketplaceAuthorizeRequestTransfer
    ): UnzerApiMarketplaceAuthorizeRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeResponseTransfer $unzerApiMarketplaceAuthorizeResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapUnzerApiMarketplaceAuthorizeResponseTransferToUnzerPaymentTransfer(
        UnzerApiMarketplaceAuthorizeResponseTransfer $unzerApiMarketplaceAuthorizeResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer;
}
