<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;

interface UnzerBasketAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    public function createBasket(UnzerBasketTransfer $unzerBasketTransfer, UnzerKeypairTransfer $unzerKeypairTransfer): UnzerBasketTransfer;
}
