<?php

namespace SprykerEco\Zed\Unzer\Communication\Oms\Command;

use Generated\Shared\Transfer\SalesOrderItemTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;

class AbstractCommand
{
    /**
     * @param array<\Orm\Zed\Sales\Persistence\SpySalesOrderItem> $salesOrderItems
     *
     * @return array<int>
     */
    protected function mapSalesOrderItemsIds(array $salesOrderItems): array
    {
        return array_map(
            function (SpySalesOrderItem $orderItem) {
                return $orderItem->getIdSalesOrderItem();
            },
            $salesOrderItems
        );
    }
}
