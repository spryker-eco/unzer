<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver;

use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface;

class UnzerPaymentProcessorStrategyResolver implements UnzerPaymentProcessorStrategyResolverInterface
{
    /**
     * @var \Closure[]
     */
    protected $strategyContainer;

    /**
     * @param \Closure[] $strategyContainer
     */
    public function __construct(array $strategyContainer)
    {
        $this->strategyContainer = $strategyContainer;
    }

    /**
     * @param string $paymentMethodName
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface
     */
    public function resolvePaymentProcessor(string $paymentMethodName): UnzerPaymentProcessorInterface
    {
        if (isset($this->strategyContainer[$paymentMethodName])) {
            return call_user_func($this->strategyContainer[$paymentMethodName]);
        }

        throw new UnzerException(sprintf('Payment processor for %s not found!', $paymentMethodName));
    }
}
