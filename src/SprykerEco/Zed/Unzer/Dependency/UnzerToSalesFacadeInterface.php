<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Dependency;

use Generated\Shared\Transfer\OrderFilterTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface UnzerToSalesFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderFilterTransfer $orderFilterTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function getOrder(OrderFilterTransfer $orderFilterTransfer): OrderTransfer;
}
