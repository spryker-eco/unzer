<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiCreateBasketRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreateBasketResponseTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;

interface UnzerBasketMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     * @param \Generated\Shared\Transfer\UnzerApiCreateBasketRequestTransfer $unzerApiCreateBasketRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiCreateBasketRequestTransfer
     */
    public function mapUnzerBasketTransferToUnzerApiCreateBasketRequestTransfer(
        UnzerBasketTransfer $unzerBasketTransfer,
        UnzerApiCreateBasketRequestTransfer $unzerApiCreateBasketRequestTransfer
    ): UnzerApiCreateBasketRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerApiCreateBasketResponseTransfer $unzerApiCreateBasketResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    public function mapUnzerApiCreateBasketResponseTransferToUnzerBasketTransfer(
        UnzerApiCreateBasketResponseTransfer $unzerApiCreateBasketResponseTransfer,
        UnzerBasketTransfer $unzerBasketTransfer
    ): UnzerBasketTransfer;
}
