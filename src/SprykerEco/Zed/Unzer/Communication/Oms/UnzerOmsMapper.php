<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication\Oms;

use Generated\Shared\Transfer\OrderFilterTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Calculation\Business\CalculationFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface;

class UnzerOmsMapper implements UnzerOmsMapperInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var \Spryker\Zed\Calculation\Business\CalculationFacadeInterface
     */
    protected $calculationFacade;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface $salesFacade
     * @param \Spryker\Zed\Calculation\Business\CalculationFacadeInterface $calculationFacade
     */
    public function __construct(
        UnzerToSalesFacadeInterface $salesFacade,
        CalculationFacadeInterface $calculationFacade
    ) {
        $this->salesFacade = $salesFacade;
        $this->calculationFacade = $calculationFacade;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function mapSpySalesOrderToOrderTransfer(SpySalesOrder $orderEntity): OrderTransfer
    {
        $orderFilterTransfer = (new OrderFilterTransfer())->setSalesOrderId($orderEntity->getIdSalesOrder());
        $orderTransfer = $this->salesFacade->getOrder($orderFilterTransfer);

        return $this->calculationFacade
            ->recalculateOrder($orderTransfer);
    }
}
