<?php

namespace SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver;

use SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface;

interface UnzerPaymentProcessorStrategyResolverInterface
{
    /**
     * @param string $paymentMethodName
     *
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface
     */
    public function resolvePaymentProcessor(string $paymentMethodName): UnzerPaymentProcessorInterface;
}
