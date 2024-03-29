<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Oms\Command;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToRefundFacadeInterface;

class RefundUnzerOmsCommand extends AbstractUnzerOmsCommand implements RefundUnzerOmsCommandInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToRefundFacadeInterface
     */
    protected $refundFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface
     */
    protected $unzerPaymentProcessorResolver;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToRefundFacadeInterface $refundFacade
     * @param \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface $unzerPaymentProcessorResolver
     */
    public function __construct(
        UnzerToRefundFacadeInterface $refundFacade,
        UnzerPaymentProcessorResolverInterface $unzerPaymentProcessorResolver
    ) {
        $this->refundFacade = $refundFacade;
        $this->unzerPaymentProcessorResolver = $unzerPaymentProcessorResolver;
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return void
     */
    public function execute(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $paymentMethodName = $this->getPaymentMethodName($orderTransfer);
        $paymentProcessor = $this->unzerPaymentProcessorResolver->resolvePaymentProcessor($paymentMethodName);

        $paymentProcessor->processRefund($refundTransfer, $orderTransfer, $salesOrderItemIds);

        $this->refundFacade->saveRefund($refundTransfer);
    }
}
