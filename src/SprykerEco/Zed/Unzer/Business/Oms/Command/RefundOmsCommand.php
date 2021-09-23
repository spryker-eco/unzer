<?php

namespace SprykerEco\Zed\Unzer\Business\Oms\Command;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorStrategyResolverInterface;
use Spryker\Zed\Refund\Business\RefundFacadeInterface;

class RefundOmsCommand extends AbstractOmsCommand implements UnzerRefundOmsCommandByOrderInterface
{
    /**
     * @var \Spryker\Zed\Refund\Business\RefundFacadeInterface
     */
    protected $refundFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorStrategyResolverInterface
     */
    protected $paymentProcessorStrategyResolver;

    /**
     * @param \Spryker\Zed\Refund\Business\RefundFacadeInterface $refundFacade
     * @param \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorStrategyResolverInterface $paymentProcessorStrategyResolver
     */
    public function __construct(
        RefundFacadeInterface $refundFacade,
        UnzerPaymentProcessorStrategyResolverInterface $paymentProcessorStrategyResolver
    ) {
        $this->refundFacade = $refundFacade;
        $this->paymentProcessorStrategyResolver = $paymentProcessorStrategyResolver;
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array $salesOrderItemIds
     *
     * @return void
     */
    public function execute(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $paymentMethodName = $this->getPaymentMethodName($orderTransfer);
        $paymentProcessor = $this->paymentProcessorStrategyResolver->resolvePaymentProcessor($paymentMethodName);

        $paymentProcessor->processRefund($refundTransfer, $orderTransfer, $salesOrderItemIds);

        $this->refundFacade->saveRefund($refundTransfer);
    }
}
