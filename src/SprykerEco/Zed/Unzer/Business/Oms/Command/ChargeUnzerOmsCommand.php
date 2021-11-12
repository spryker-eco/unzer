<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Oms\Command;

use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerChargeablePaymentProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface;

class ChargeUnzerOmsCommand extends AbstractUnzerOmsCommand implements UnzerOmsCommandInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface
     */
    protected $unzerPaymentProcessorStrategyResolver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface $paymentProcessorStrategyResolver
     */
    public function __construct(
        UnzerPaymentProcessorResolverInterface $paymentProcessorStrategyResolver
    ) {
        $this->unzerPaymentProcessorStrategyResolver = $paymentProcessorStrategyResolver;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return void
     */
    public function execute(OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $paymentMethodName = $this->getPaymentMethodName($orderTransfer);
        $paymentProcessor = $this->unzerPaymentProcessorStrategyResolver->resolvePaymentProcessor($paymentMethodName);

        if ($paymentProcessor instanceof UnzerChargeablePaymentProcessorInterface) {
            $paymentProcessor->processCharge($orderTransfer, $salesOrderItemIds);
        }
    }
}
