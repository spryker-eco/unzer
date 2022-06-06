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
class UnzerRefundCommandByOrderPlugin extends AbstractPlugin implements CommandByOrderInterface
{
    /**
     * {@inheritDoc}
     * - Requires `OrderTransfer.payments.paymentProvider.paymentMethod` to be set.
     * - Requires `OrderTransfer.orderReference` to be set.
     * - Requires `OrderTransfer.idSalesOrderItem` to be set.
     * - Requires `ItemTransfer.groupKey` to be set for each element at `RefundTransfer.items`
     * - Requires `ItemTransfer.unzerParticipantId` to be set for each element at `RefundTransfer.items`
     * - Requires `ItemTransfer.refundableAmount` to be set for each element at `RefundTransfer.items`
     * - Requires `ItemTransfer.quantity` to be set for each element at `RefundTransfer.items`
     * - Requires `ItemTransfer.idSalesOrderItem` to be set for each element at `RefundTransfer.items`
     * - Uses a strategy to resolve Unzer Expense Refund.
     * - Executes Unzer API Refund request.
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
        $this->getFactory()->createRefundUnzerOmsCommand()->execute($orderItems, $orderEntity, $data);

        return [];
    }
}
