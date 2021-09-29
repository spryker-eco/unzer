<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication\Oms\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Refund\Business\RefundFacadeInterface;
use SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface;
use SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapperInterface;

class RefundCommand extends AbstractCommand implements UnzerOmsCommandByOrderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface
     */
    protected $unzerFacade;

    /**
     * @var \Spryker\Zed\Refund\Business\RefundFacadeInterface
     */
    protected $refundFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapperInterface
     */
    protected $unzerOmsMapper;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface $unzerFacade
     * @param \Spryker\Zed\Refund\Business\RefundFacadeInterface $refundFacade
     * @param \SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapperInterface $unzerOmsMapper
     */
    public function __construct(
        UnzerFacadeInterface $unzerFacade,
        RefundFacadeInterface $refundFacade,
        UnzerOmsMapperInterface $unzerOmsMapper
    ) {
        $this->unzerFacade = $unzerFacade;
        $this->refundFacade = $refundFacade;
        $this->unzerOmsMapper = $unzerOmsMapper;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $salesOrderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return void
     */
    public function execute(array $salesOrderItems, SpySalesOrder $salesOrderEntity, ReadOnlyArrayObject $data): void
    {
        $orderTransfer = $this->unzerOmsMapper->mapSpySalesOrderToOrderTransfer($salesOrderEntity);
        $salesOrderItemIds = $this->mapSalesOrderItemsIds($salesOrderItems);

        $refundTransfer = $this->refundFacade
            ->calculateRefund($salesOrderItems, $salesOrderEntity);

        $this->unzerFacade
            ->executeRefundOmsCommand($refundTransfer, $orderTransfer, $salesOrderItemIds);
    }
}
