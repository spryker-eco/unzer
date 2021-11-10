<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver;

use SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface;

interface UnzerPaymentProcessorResolverInterface
{
    /**
     * @param string $paymentMethodName
     *
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface
     */
    public function resolvePaymentProcessor(string $paymentMethodName): UnzerPaymentProcessorInterface;
}
