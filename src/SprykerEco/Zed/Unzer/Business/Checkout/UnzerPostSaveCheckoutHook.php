<?php

namespace SprykerEco\Zed\Unzer\Business\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as SharedUnzerConfig;
use SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorStrategyResolverInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerPostSaveCheckoutHook implements UnzerCheckoutHookInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface
     */
    protected $unzerCheckoutHookMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface
     */
    protected $unzerPaymentSaver;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorStrategyResolverInterface
     */
    protected $paymentProcessorStrategyResolver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface $unzerCheckoutHookMapper
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface $unzerPaymentSaver
     * @param \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorStrategyResolverInterface $paymentProcessorStrategyResolver
     */
    public function __construct(
        UnzerCheckoutMapperInterface $unzerCheckoutHookMapper,
        UnzerConfig $unzerConfig,
        UnzerPaymentSaverInterface $unzerPaymentSaver,
        UnzerPaymentProcessorStrategyResolverInterface $paymentProcessorStrategyResolver
    ) {
        $this->unzerCheckoutHookMapper = $unzerCheckoutHookMapper;
        $this->unzerConfig = $unzerConfig;
        $this->unzerPaymentSaver = $unzerPaymentSaver;
        $this->paymentProcessorStrategyResolver = $paymentProcessorStrategyResolver;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     *
     * @return void
     */
    public function execute(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse): void
    {
        if ($quoteTransfer->getPayment()->getPaymentProvider() !== SharedUnzerConfig::PROVIDER_NAME) {
            return;
        }

        $paymentMethod = $quoteTransfer->getPayment()->getPaymentSelection();
        $paymentProcessor = $this->paymentProcessorStrategyResolver->resolvePaymentProcessor($paymentMethod);

        $unzerPaymentTransfer = $paymentProcessor->processOrderPayment($quoteTransfer, $checkoutResponse->getSaveOrder());

        $checkoutResponse->setRedirectUrl($unzerPaymentTransfer->getRedirectUrl());
        $checkoutResponse->setIsExternalRedirect(true);
        $quoteTransfer->getPayment()->setUnzerPayment($unzerPaymentTransfer);

        $this->unzerPaymentSaver->savePaymentEntities($unzerPaymentTransfer, UnzerConstants::OMS_STATUS_PAYMENT_PENDING);
    }
}
