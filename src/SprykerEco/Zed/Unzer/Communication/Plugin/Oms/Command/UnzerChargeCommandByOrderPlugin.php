<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication\Plugin\Oms\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * @method \SprykerEco\Zed\Unzer\UnzerConfig getConfig()
 * @method \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Unzer\Communication\UnzerCommunicationFactory getFactory()
 */
class UnzerChargeCommandByOrderPlugin extends AbstractPlugin implements CommandByOrderInterface
{
    /**
     * {@inheritDoc}
     * - Requires `OrderTransfer.orderReference` to be set.
     * - Requires `OrderTransfer.idSalesOrderItem` to be set.
     * - Requires `ItemTransfer.shipment.idSalesShipment` to be set for each element at `OrderTransfer.items`.
     * - Requires `ExpenseTransfer.shipment.idSalesShipment` to be set for each element at `OrderTransfer.expenses`.
     * - Executes Unzer API Charge request.
     * - Saves Unzer payment details to Persistence.
     * - Throws `UnzerApiException` if API call failed.
     * - Throws `UnzerException` if Unzer payment not found in Persistence.
     *
     * @api
     *
     * @param array<\Orm\Zed\Sales\Persistence\SpySalesOrderItem> $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array<null>
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data): array
    {
        $this->getFactory()->createChargeUnzerOmsCommand()->execute($orderItems, $orderEntity, $data);

        return [];
    }
}
