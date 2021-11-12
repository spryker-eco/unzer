<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication\Oms\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface;
use SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapperInterface;

class ChargeUnzerOmsCommand extends AbstractUnzerOmsCommand implements UnzerOmsCommandInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface
     */
    protected $unzerFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapperInterface
     */
    protected $unzerOmsMapper;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface $unzerFacade
     * @param \SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapperInterface $unzerOmsMapper
     */
    public function __construct(
        UnzerFacadeInterface $unzerFacade,
        UnzerOmsMapperInterface $unzerOmsMapper
    ) {
        $this->unzerFacade = $unzerFacade;
        $this->unzerOmsMapper = $unzerOmsMapper;
    }

    /**
     * @param array<\Orm\Zed\Sales\Persistence\SpySalesOrderItem> $salesOrderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return void
     */
    public function execute(array $salesOrderItems, SpySalesOrder $salesOrderEntity, ReadOnlyArrayObject $data): void
    {
        $orderTransfer = $this->unzerOmsMapper->mapSpySalesOrderToOrderTransfer($salesOrderEntity);
        $salesOrderItemIds = $this->mapSalesOrderItemsIds($salesOrderItems);

        $this->unzerFacade->executeChargeOmsCommand($orderTransfer, $salesOrderItemIds);
    }
}
