<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiCreateBasketRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreateBasketResponseTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;

class UnzerBasketMapper implements UnzerBasketMapperInterface
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
    ): UnzerApiCreateBasketRequestTransfer {
        $unzerApiCreateBasketRequestTransfer->fromArray($unzerBasketTransfer->toArray(), true);
        $unzerApiCreateBasketRequestTransfer->setOrderId($unzerBasketTransfer->getOrderId());

        return $unzerApiCreateBasketRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiCreateBasketResponseTransfer $unzerApiCreateBasketResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    public function mapUnzerApiCreateBasketResponseTransferToUnzerBasketTransfer(
        UnzerApiCreateBasketResponseTransfer $unzerApiCreateBasketResponseTransfer,
        UnzerBasketTransfer $unzerBasketTransfer
    ): UnzerBasketTransfer {
        $unzerBasketTransfer->fromArray($unzerApiCreateBasketResponseTransfer->toArray(), true);

        return $unzerBasketTransfer;
    }
}
