<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpensesRefundStrategyResolver;

use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpensesRefundStrategyInterface;

interface UnzerExpensesRefundStrategyResolverInterface
{
    /**
     * @param int $strategyConfigKey
     *
     * @return \SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpensesRefundStrategyInterface
     */
    public function resolveRefundStrategy(int $strategyConfigKey): UnzerExpensesRefundStrategyInterface;
}
