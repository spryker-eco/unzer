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
    protected $unzerPaymentProcessorCollection;

    /**
     * @param array<\Closure> $unzerPaymentProcessorCollection
     */
    public function __construct(array $unzerPaymentProcessorCollection)
    {
        $this->unzerPaymentProcessorCollection = $unzerPaymentProcessorCollection;
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
        if (isset($this->unzerPaymentProcessorCollection[$paymentMethodName])) {
            return call_user_func($this->unzerPaymentProcessorCollection[$paymentMethodName]);
        }

        throw new UnzerException(sprintf('Payment processor for %s not found!', $paymentMethodName));
    }
}
