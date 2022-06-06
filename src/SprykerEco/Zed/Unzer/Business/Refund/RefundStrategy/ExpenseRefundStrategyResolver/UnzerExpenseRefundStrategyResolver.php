<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpenseRefundStrategyResolver;

use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpenseRefundStrategyInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerExpenseRefundStrategyResolver implements UnzerExpenseRefundStrategyResolverInterface
{
    /**
     * @var array<int, \Closure>
     */
    protected $unzerRefundStrategies;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @param array<int, \Closure> $unzerRefundStrategiesCollection
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(
        array $unzerRefundStrategiesCollection,
        UnzerConfig $unzerConfig
    ) {
        $this->unzerRefundStrategies = $unzerRefundStrategiesCollection;
        $this->unzerConfig = $unzerConfig;
    }

    /**
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return \SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpenseRefundStrategyInterface
     */
    public function resolveRefundStrategyFromConfig(): UnzerExpenseRefundStrategyInterface
    {
        $strategyConfigKey = $this->unzerConfig->getExpensesRefundStrategyKey();
        if (isset($this->unzerRefundStrategies[$strategyConfigKey])) {
            return call_user_func($this->unzerRefundStrategies[$strategyConfigKey]);
        }

        throw new UnzerException(sprintf('Unzer refund strategy for config key %s not found!', $strategyConfigKey));
    }
}
