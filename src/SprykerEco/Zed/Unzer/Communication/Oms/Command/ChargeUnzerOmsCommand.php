<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication\Oms\Command;

use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToCalculationFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface;

class ChargeUnzerOmsCommand extends AbstractUnzerOmsCommand implements UnzerOmsCommandInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface
     */
    protected $unzerFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToCalculationFacadeInterface
     */
    protected $calculationFacade;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface $unzerFacade
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface $salesFacade
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToCalculationFacadeInterface $calculationFacade
     */
    public function __construct(
        UnzerFacadeInterface $unzerFacade,
        UnzerToSalesFacadeInterface $salesFacade,
        UnzerToCalculationFacadeInterface $calculationFacade
    ) {
        $this->unzerFacade = $unzerFacade;
        $this->salesFacade = $salesFacade;
        $this->calculationFacade = $calculationFacade;
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
        $orderTransfer = $this->getSalesOrderItemBySalesOrderItemEntity($salesOrderEntity);
        $salesOrderItemIds = $this->extractSalesOrderItemsIds($salesOrderItems);

        $this->unzerFacade->executeChargeOmsCommand($orderTransfer, $salesOrderItemIds);
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function getSalesOrderItemBySalesOrderItemEntity(SpySalesOrder $salesOrderEntity): OrderTransfer
    {
        $orderTransfer = $this->salesFacade
            ->getOrderByIdSalesOrder($salesOrderEntity->getIdSalesOrder());

        return $this->calculationFacade
            ->recalculateOrder($orderTransfer);
    }
}
