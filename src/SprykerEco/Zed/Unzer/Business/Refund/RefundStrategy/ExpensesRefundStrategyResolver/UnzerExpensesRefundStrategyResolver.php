<?php

namespace SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpensesRefundStrategyResolver;

use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpensesRefundStrategyInterface;

class UnzerExpensesRefundStrategyResolver implements UnzerExpensesRefundStrategyResolverInterface
{
    /**
     * @var array<int,\Closure>
     */
    protected $unzerRefundStrategiesCollection;

    /**
     * @param array<int,\Closure> $unzerRefundStrategiesCollection
     */
    public function __construct(array $unzerRefundStrategiesCollection)
    {
        $this->unzerRefundStrategiesCollection = $unzerRefundStrategiesCollection;
    }

    /**
     * @param int $strategyConfigKey
     *
     * @return UnzerExpensesRefundStrategyInterface
     *
     * @throws UnzerException
     */
    public function resolveRefundStrategy(int $strategyConfigKey): UnzerExpensesRefundStrategyInterface
    {
        if (isset($this->unzerRefundStrategiesCollection[$strategyConfigKey])) {
            return call_user_func($this->unzerRefundStrategiesCollection[$strategyConfigKey]);
        }

        throw new UnzerException(sprintf('Unzer refund strategy for config key %s not found!', $strategyConfigKey));
    }
}
