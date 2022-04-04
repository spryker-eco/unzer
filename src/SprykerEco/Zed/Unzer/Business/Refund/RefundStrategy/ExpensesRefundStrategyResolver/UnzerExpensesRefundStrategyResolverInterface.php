<?php

namespace SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpensesRefundStrategyResolver;

use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpensesRefundStrategyInterface;

interface UnzerExpensesRefundStrategyResolverInterface
{
    /**
     * @param int $strategyConfigKey
     *
     * @return UnzerExpensesRefundStrategyInterface
     */
    public function resolveRefundStrategy(int $strategyConfigKey): UnzerExpensesRefundStrategyInterface;
}
