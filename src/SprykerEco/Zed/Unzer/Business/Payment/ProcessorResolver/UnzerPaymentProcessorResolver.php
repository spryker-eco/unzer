<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver;

use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface;

class UnzerPaymentProcessorResolver implements UnzerPaymentProcessorResolverInterface
{
    /**
     * @var array<\Closure>
     */
    protected $unzerPaymentProcessorsCollection;

    /**
     * @param array<\Closure> $unzerPaymentProcessorsCollection
     */
    public function __construct(array $unzerPaymentProcessorsCollection)
    {
        $this->unzerPaymentProcessorsCollection = $unzerPaymentProcessorsCollection;
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
        if (isset($this->unzerPaymentProcessorsCollection[$paymentMethodName])) {
            return call_user_func($this->unzerPaymentProcessorsCollection[$paymentMethodName]);
        }

        throw new UnzerException(sprintf('Payment processor for %s not found!', $paymentMethodName));
    }
}
