<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Mapper;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\UnzerRefundItemCollectionTransfer;
use Generated\Shared\Transfer\UnzerRefundItemTransfer;

interface UnzerMarketplaceRefundMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\ItemCollectionTransfer $itemCollectionTransfer
     * @param \Generated\Shared\Transfer\UnzerRefundItemCollectionTransfer $unzerRefundItemCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerRefundItemCollectionTransfer
     */
    public function mapItemCollectionTransferToUnzerRefundItemCollection(
        ItemCollectionTransfer $itemCollectionTransfer,
        UnzerRefundItemCollectionTransfer $unzerRefundItemCollectionTransfer
    ): UnzerRefundItemCollectionTransfer;

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\UnzerRefundItemTransfer $unzerRefundItemTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerRefundItemTransfer
     */
    public function mapItemTransferToUnzerRefundItemTransfer(
        ItemTransfer $itemTransfer,
        UnzerRefundItemTransfer $unzerRefundItemTransfer
    ): UnzerRefundItemTransfer;
}
