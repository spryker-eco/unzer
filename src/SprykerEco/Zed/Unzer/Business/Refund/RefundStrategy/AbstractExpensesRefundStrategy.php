<?php

namespace SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy;

use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use SprykerEco\Zed\Unzer\UnzerConstants;

abstract class AbstractExpensesRefundStrategy
{
    /**
     * @param PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    protected function isPaymentUnzerOrderItemAlreadyRefunded(
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
        int $idSalesOrderItem
    ): bool
    {
        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            if (
                $paymentUnzerOrderItem->getIdSalesOrderItemOrFail() === $idSalesOrderItem
                && $paymentUnzerOrderItem->getStatusOrFail() === UnzerConstants::OMS_STATUS_CHARGE_REFUNDED
            ) {
                return true;
            }
        }

        return false;
    }
}
