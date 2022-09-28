<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiAuthorizeResponseTransfer;
use Generated\Shared\Transfer\UnzerApiErrorResponseTransfer;
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
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\UnzerApiAuthorizeRequestTransfer $unzerApiAuthorizeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiAuthorizeRequestTransfer
     */
    public function mapUnzerPaymentTransferToUnzerApiAuthorizeRequestTransfer(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        UnzerApiAuthorizeRequestTransfer $unzerApiAuthorizeRequestTransfer
    ): UnzerApiAuthorizeRequestTransfer;

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

    /**
     * @param \Generated\Shared\Transfer\UnzerApiAuthorizeResponseTransfer $unzerApiAuthorizeResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapUnzerApiAuthorizeResponseTransferToUnzerPaymentTransfer(
        UnzerApiAuthorizeResponseTransfer $unzerApiAuthorizeResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapUnzerApiErrorResponseTransferToUnzerPaymentTransfer(
        UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer;
}
