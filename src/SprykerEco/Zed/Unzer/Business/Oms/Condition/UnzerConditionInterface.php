<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

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
