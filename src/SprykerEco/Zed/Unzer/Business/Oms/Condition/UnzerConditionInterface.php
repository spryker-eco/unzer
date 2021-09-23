<?php

namespace SprykerEco\Zed\Unzer\Business\Oms\Condition;

interface UnzerConditionInterface
{
    /**
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function check(int $idSalesOrderItem): bool;
}
