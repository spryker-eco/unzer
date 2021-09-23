<?php

namespace SprykerEco\Zed\Unzer\Dependency;

interface UnzerToSalesFacadeInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @throws \Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function getOrderByIdSalesOrder($idSalesOrder);
}
