<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpenseRefundStrategyResolver;

use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpenseRefundStrategyInterface;

interface UnzerExpenseRefundStrategyResolverInterface
{
    /**
     * @return \SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpenseRefundStrategyInterface
     */
    public function resolveRefundStrategyFromConfig(): UnzerExpenseRefundStrategyInterface;
}
