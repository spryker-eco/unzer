<?php

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Mapper;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\UnzerRefundItemCollectionTransfer;
use Generated\Shared\Transfer\UnzerRefundItemTransfer;

interface UnzerMarketplaceRefundMapperInterface
{
    /**
     * @param ItemCollectionTransfer $itemCollectionTransfer
     * @param UnzerRefundItemCollectionTransfer $unzerRefundItemCollectionTransfer
     *
     * @return UnzerRefundItemCollectionTransfer
     */
    public function mapItemCollectionTransferToUnzerRefundItemCollection(
        ItemCollectionTransfer $itemCollectionTransfer,
        UnzerRefundItemCollectionTransfer $unzerRefundItemCollectionTransfer
    ): UnzerRefundItemCollectionTransfer;

    /**
     * @param ItemTransfer $itemTransfer
     * @param UnzerRefundItemTransfer $unzerRefundItemTransfer
     *
     * @return UnzerRefundItemTransfer
     */
    public function mapItemTransferToUnzerRefundItemTransfer(ItemTransfer $itemTransfer, UnzerRefundItemTransfer $unzerRefundItemTransfer): UnzerRefundItemTransfer;
}
