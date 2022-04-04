<?php

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
     * @param ItemCollectionTransfer $itemCollectionTransfer
     * @param UnzerRefundItemCollectionTransfer $unzerRefundItemCollectionTransfer
     *
     * @return UnzerRefundItemCollectionTransfer
     */
    public function mapItemCollectionTransferToUnzerRefundItemCollection(
        ItemCollectionTransfer $itemCollectionTransfer,
        UnzerRefundItemCollectionTransfer $unzerRefundItemCollectionTransfer
    ): UnzerRefundItemCollectionTransfer
    {
        foreach ($itemCollectionTransfer->getItems() as $itemTransfer) {
            $groupKey = $itemTransfer->getGroupKey();
            if ($unzerRefundItemCollectionTransfer->getUnzerRefundItems()->offsetExists($groupKey)) {
                /** @var UnzerRefundItemTransfer $unzerRefundItemTransfer */
                $unzerRefundItemTransfer = $unzerRefundItemCollectionTransfer->getUnzerRefundItems()->offsetGet($groupKey);
                $unzerRefundItemTransfer
                    ->setQuantity(static::DEFAULT_QUANTITY)
                    ->setAmountGross(
                        $unzerRefundItemTransfer->getAmountGross() + $itemTransfer->getSumPriceToPayAggregationOrFail() / UnzerConstants::INT_TO_FLOAT_DIVIDER
                    );

                continue;
            }

            $unzerRefundItemCollectionTransfer->getUnzerRefundItems()->offsetSet(
                $groupKey,
                $this->mapItemTransferToUnzerRefundItemTransfer($itemTransfer, new UnzerRefundItemTransfer()),
            );
        }

        return $unzerRefundItemCollectionTransfer;
    }

    /**
     * @param ItemTransfer $itemTransfer
     * @param UnzerRefundItemTransfer $unzerRefundItemTransfer
     *
     * @return UnzerRefundItemTransfer
     */
    public function mapItemTransferToUnzerRefundItemTransfer(ItemTransfer $itemTransfer, UnzerRefundItemTransfer $unzerRefundItemTransfer): UnzerRefundItemTransfer
    {
        return $unzerRefundItemTransfer
            ->setAmountGross($itemTransfer->getSumPriceToPayAggregationOrFail() * UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setParticipantId($itemTransfer->getUnzerParticipantIdOrFail())
            ->setAmountGross($itemTransfer->getSumPriceToPayAggregationOrFail() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setQuantity($itemTransfer->getQuantityOrFail())
            ->setBasketItemReferenceId($itemTransfer->getGroupKeyOrFail());
    }
}
