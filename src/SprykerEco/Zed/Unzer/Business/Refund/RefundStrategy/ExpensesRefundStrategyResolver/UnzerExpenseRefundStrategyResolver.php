<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpensesRefundStrategyResolver;

use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpensesRefundStrategyInterface;

class UnzerExpenseRefundStrategyResolver implements UnzerExpenseRefundStrategyResolverInterface
{
    /**
     * @var array<int, \Closure>
     */
    protected $unzerRefundStrategies;

    /**
     * @param array<int, \Closure> $unzerRefundStrategiesCollection
     */
    public function __construct(array $unzerRefundStrategiesCollection)
    {
        $this->unzerRefundStrategies = $unzerRefundStrategiesCollection;
    }

    /**
     * @param int $strategyConfigKey
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return \SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpensesRefundStrategyInterface
     */
    public function resolveRefundStrategy(int $strategyConfigKey): UnzerExpensesRefundStrategyInterface
    {
        if (isset($this->unzerRefundStrategies[$strategyConfigKey])) {
            return call_user_func($this->unzerRefundStrategies[$strategyConfigKey]);
        }

        throw new UnzerException(sprintf('Unzer refund strategy for config key %s not found!', $strategyConfigKey));
    }
}
