<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiMarketplaceRefundRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRefundRequestTransfer;
use Generated\Shared\Transfer\UnzerRefundTransfer;

interface UnzerRefundMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerRefundTransfer $unzerRefundTransfer
     * @param \Generated\Shared\Transfer\UnzerApiMarketplaceRefundRequestTransfer $unzerApiMarketplaceRefundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiMarketplaceRefundRequestTransfer
     */
    public function mapUnzerRefundTransferToUnzerApiMarketplaceRefundRequestTransfer(
        UnzerRefundTransfer $unzerRefundTransfer,
        UnzerApiMarketplaceRefundRequestTransfer $unzerApiMarketplaceRefundRequestTransfer
    ): UnzerApiMarketplaceRefundRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerRefundTransfer $unzerRefundTransfer
     * @param \Generated\Shared\Transfer\UnzerApiRefundRequestTransfer $unzerApiRefundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiRefundRequestTransfer
     */
    public function mapUnzerRefundTransferToUnzerApiRefundRequestTransfer(
        UnzerRefundTransfer $unzerRefundTransfer,
        UnzerApiRefundRequestTransfer $unzerApiRefundRequestTransfer
    ): UnzerApiRefundRequestTransfer;
}
