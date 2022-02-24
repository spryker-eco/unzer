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
use SprykerEco\Zed\Unzer\Dependency\UnzerToRefundFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToShipmentFacadeInterface;

class RefundUnzerOmsCommand extends AbstractUnzerOmsCommand implements UnzerOmsCommandInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface
     */
    protected $unzerFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToRefundFacadeInterface
     */
    protected $refundFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToShipmentFacadeInterface
     */
    protected $shipmentFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapperInterface
     */
    protected $unzerOmsMapper;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface $unzerFacade
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToRefundFacadeInterface $refundFacade
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToShipmentFacadeInterface $shipmentFacade
     * @param \SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapperInterface $unzerOmsMapper
     */
    public function __construct(
        UnzerFacadeInterface $unzerFacade,
        UnzerToRefundFacadeInterface $refundFacade,
        UnzerToShipmentFacadeInterface $shipmentFacade,
        UnzerOmsMapperInterface $unzerOmsMapper
    ) {
        $this->unzerFacade = $unzerFacade;
        $this->refundFacade = $refundFacade;
        $this->shipmentFacade = $shipmentFacade;
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

        $orderTransfer = $this->shipmentFacade->hydrateOrderShipment($orderTransfer);
        $refundTransfer = $this->refundFacade
            ->calculateRefund($salesOrderItems, $salesOrderEntity);

        $this->unzerFacade
            ->executeRefundOmsCommand($refundTransfer, $orderTransfer, $salesOrderItemIds);
    }
}
