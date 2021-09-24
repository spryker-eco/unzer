<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiCreatePaymentResourceRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreatePaymentResourceResponseTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;

interface UnzerPaymentResourceMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer
     * @param \Generated\Shared\Transfer\UnzerApiCreatePaymentResourceRequestTransfer $unzerApiCreatePaymentResourceRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiCreatePaymentResourceRequestTransfer
     */
    public function mapUnzerPaymentResourceTransferToUnzerApiCreatePaymentResourceRequestTransfer(
        UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer,
        UnzerApiCreatePaymentResourceRequestTransfer $unzerApiCreatePaymentResourceRequestTransfer
    ): UnzerApiCreatePaymentResourceRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerApiCreatePaymentResourceResponseTransfer $unzerApiCreatePaymentResourceResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentResourceTransfer
     */
    public function mapUnzerApiCreatePaymentResourceTransferResponseToUnzerPaymentResourceTransfer(
        UnzerApiCreatePaymentResourceResponseTransfer $unzerApiCreatePaymentResourceResponseTransfer,
        UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer
    ): UnzerPaymentResourceTransfer;
}
