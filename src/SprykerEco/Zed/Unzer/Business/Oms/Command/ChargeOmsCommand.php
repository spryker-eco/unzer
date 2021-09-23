<?php

namespace SprykerEco\Zed\Unzer\Business\Oms\Command;

use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorStrategyResolverInterface;

class ChargeOmsCommand extends AbstractOmsCommand implements UnzerOmsCommandByOrderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorStrategyResolverInterface
     */
    protected $paymentProcessorStrategyResolver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorStrategyResolverInterface $paymentProcessorStrategyResolver
     */
    public function __construct(
        UnzerPaymentProcessorStrategyResolverInterface $paymentProcessorStrategyResolver
    ) {
        $this->paymentProcessorStrategyResolver = $paymentProcessorStrategyResolver;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int[] $salesOrderItemIds
     *
     * @return void
     */
    public function execute(OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $paymentMethodName = $this->getPaymentMethodName($orderTransfer);
        $paymentProcessor = $this->paymentProcessorStrategyResolver->resolvePaymentProcessor($paymentMethodName);

        $paymentProcessor->processCharge($orderTransfer, $salesOrderItemIds);
    }
}
