<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerBasketTransfer;

interface UnzerBasketAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    public function createBasket(UnzerBasketTransfer $unzerBasketTransfer): UnzerBasketTransfer;
}
