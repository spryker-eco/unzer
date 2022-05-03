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
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerMarketplaceRefundMapper implements UnzerMarketplaceRefundMapperInterface
{
    /**
     * @var int
     */
    protected const DEFAULT_QUANTITY = 0;

    /**
     * @param \Generated\Shared\Transfer\ItemCollectionTransfer $itemCollectionTransfer
     * @param \Generated\Shared\Transfer\UnzerRefundItemCollectionTransfer $unzerRefundItemCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerRefundItemCollectionTransfer
     */
    public function mapItemCollectionTransferToUnzerRefundItemCollection(
        ItemCollectionTransfer $itemCollectionTransfer,
        UnzerRefundItemCollectionTransfer $unzerRefundItemCollectionTransfer
    ): UnzerRefundItemCollectionTransfer {
        foreach ($itemCollectionTransfer->getItems() as $itemTransfer) {
            $unzerRefundItemCollectionTransfer->addUnzerRefundItem($this->mapItemTransferToUnzerRefundItemTransfer($itemTransfer, new UnzerRefundItemTransfer()));
        }

        return $unzerRefundItemCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\UnzerRefundItemTransfer $unzerRefundItemTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerRefundItemTransfer
     */
    protected function mapItemTransferToUnzerRefundItemTransfer(
        ItemTransfer $itemTransfer,
        UnzerRefundItemTransfer $unzerRefundItemTransfer
    ): UnzerRefundItemTransfer {
        return $unzerRefundItemTransfer
            ->setParticipantId($itemTransfer->getUnzerParticipantIdOrFail())
            ->setAmountGross($itemTransfer->getRefundableAmountOrFail() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setQuantity($itemTransfer->getQuantityOrFail())
            ->setBasketItemReferenceId(
                sprintf(
                    UnzerConstants::UNZER_BASKET_ITEM_REFERENCE_ID_TEMPLATE,
                    $itemTransfer->getGroupKeyOrFail(),
                    $itemTransfer->getIdSalesOrderItemOrFail(),
                ),
            );
    }
}
