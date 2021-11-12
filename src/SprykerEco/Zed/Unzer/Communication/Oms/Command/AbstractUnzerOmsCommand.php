<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication\Oms\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;

abstract class AbstractUnzerOmsCommand
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
            $salesOrderItems,
        );
    }
}
