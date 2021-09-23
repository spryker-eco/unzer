<?php

namespace SprykerEco\Zed\Unzer\Business;

use SprykerEco\Shared\Unzer\UnzerConfig;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerAuthorizePaymentMapper;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerAuthorizePaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerBasketMapper;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerBasketMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerChargeMapper;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerChargeMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerCustomerMapper;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerCustomerMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMapper;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerPaymentResourceMapper;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerPaymentResourceMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerRefundMapper;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerRefundMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerAuthorizeAdapter;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerAuthorizeAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerBasketAdapter;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerBasketAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerChargeAdapter;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerChargeAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapter;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapter;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentResourceAdapter;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentResourceAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapter;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutPostSaveMapper;
use SprykerEco\Zed\Unzer\Business\Checkout\UnzerCheckoutHookInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\UnzerPostSaveCheckoutHook;
use SprykerEco\Zed\Unzer\Business\Notification\NotificationProcessor;
use SprykerEco\Zed\Unzer\Business\Notification\NotificationProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Oms\Command\ChargeOmsCommand;
use SprykerEco\Zed\Unzer\Business\Oms\Command\RefundOmsCommand;
use SprykerEco\Zed\Unzer\Business\Oms\Command\UnzerOmsCommandByOrderInterface;
use SprykerEco\Zed\Unzer\Business\Oms\Command\UnzerRefundOmsCommandByOrderInterface;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsAuthorizeCanceledOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsAuthorizeFailedOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsAuthorizePendingOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsAuthorizeSucceededOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsChargebackOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsChargeFailedOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsPaymentCompletedOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Filter\UnzerPaymentMethodFilter;
use SprykerEco\Zed\Unzer\Business\Payment\Filter\UnzerPaymentMethodFilterInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapper;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerChargeProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerMarketplaceCreditCardChargeProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\MarketplaceBankTransferProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\MarketplaceCreditCardProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\MarketplaceRefundProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorStrategyResolver;
use SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorStrategyResolverInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaver;
use SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface;
use SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteExpanderMapper;
use SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteExpanderMapperInterface;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerQuoteExpander;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerQuoteExpanderInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReader;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerWriter;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepository;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;
use SprykerEco\Zed\Unzer\UnzerDependencyProvider;
use SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface;
use Spryker\Client\Quote\QuoteClientInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Refund\Business\RefundFacadeInterface;

/**
 * @method \SprykerEco\Zed\Unzer\UnzerConfig getConfig()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerQueryContainer getQueryContainer()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface getEntityManager()
 */
class UnzerBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\Unzer\Business\Quote\UnzerQuoteExpanderInterface
     */
    public function createUnzerQuoteExpander(): UnzerQuoteExpanderInterface
    {
        return new UnzerQuoteExpander(
            $this->createUnzerCustomerAdapter(),
            $this->createUnzerQuoteExpanderMapper(),
            $this->getQuoteClient(),
            $this->getConfig(),
            $this->createUnzerReader()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Checkout\UnzerCheckoutHookInterface
     */
    public function createUnzerPostSaveCheckoutHook(): UnzerCheckoutHookInterface
    {
        return new UnzerPostSaveCheckoutHook(
            $this->createUnzerCheckoutHookMapper(),
            $this->getConfig(),
            $this->createUnzerPaymentSaver(),
            $this->createPaymentProcessorStrategyResolver()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface
     */
    public function createUnzerCheckoutHookMapper(): UnzerCheckoutMapperInterface
    {
        return new UnzerCheckoutPostSaveMapper(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    public function createUnzerReader(): UnzerReaderInterface
    {
        return new UnzerReader(
            $this->createUnzerRepository()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface
     */
    public function createUnzerRepository(): UnzerRepositoryInterface
    {
        return new UnzerRepository();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteExpanderMapperInterface
     */
    public function createUnzerQuoteExpanderMapper(): UnzerQuoteExpanderMapperInterface
    {
        return new UnzerQuoteExpanderMapper();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface
     */
    public function createUnzerWriter(): UnzerWriterInterface
    {
        return new UnzerWriter(
            $this->getEntityManager(),
            $this->getRepository(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface
     */
    public function createUnzerPaymentSaver(): UnzerPaymentSaverInterface
    {
        return new UnzerPaymentSaver(
            $this->createUnzerReader(),
            $this->createUnzerWriter(),
            $this->getConfig(),
            $this->createUnzerPaymentMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface
     */
    public function createUnzerCustomerAdapter(): UnzerCustomerAdapterInterface
    {
        return new UnzerCustomerAdapter(
            $this->getUnzerApiFacade(),
            $this->getConfig(),
            $this->createUnzerCustomerMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerCustomerMapperInterface
     */
    public function createUnzerCustomerMapper(): UnzerCustomerMapperInterface
    {
        return new UnzerCustomerMapper();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerBasketAdapterInterface
     */
    public function createUnzerBasketAdapter(): UnzerBasketAdapterInterface
    {
        return new UnzerBasketAdapter(
            $this->getUnzerApiFacade(),
            $this->createUnzerBasketMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerBasketMapperInterface
     */
    public function createUnzerBasketMapper(): UnzerBasketMapperInterface
    {
        return new UnzerBasketMapper();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentResourceAdapterInterface
     */
    public function createUnzerPaymentResourceAdapter(): UnzerPaymentResourceAdapterInterface
    {
        return new UnzerPaymentResourceAdapter(
            $this->getUnzerApiFacade(),
            $this->createUnzerPaymentResourceMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerPaymentResourceMapperInterface
     */
    public function createUnzerPaymentResourceMapper(): UnzerPaymentResourceMapperInterface
    {
        return new UnzerPaymentResourceMapper();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerAuthorizeAdapterInterface
     */
    public function createUnzerAuthorizeAdapter(): UnzerAuthorizeAdapterInterface
    {
        return new UnzerAuthorizeAdapter(
            $this->getUnzerApiFacade(),
            $this->createUnzerAuthorizeMapper(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerAuthorizePaymentMapperInterface
     */
    public function createUnzerAuthorizeMapper(): UnzerAuthorizePaymentMapperInterface
    {
        return new UnzerAuthorizePaymentMapper(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerChargeAdapterInterface
     */
    public function createUnzerChargeAdapter(): UnzerChargeAdapterInterface
    {
        return new UnzerChargeAdapter(
            $this->getUnzerApiFacade(),
            $this->createUnzerChargeMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerChargeMapperInterface
     */
    public function createUnzerChargeMapper(): UnzerChargeMapperInterface
    {
        return new UnzerChargeMapper(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface
     */
    public function createUnzerPaymentAdapter(): UnzerPaymentAdapterInterface
    {
        return new UnzerPaymentAdapter(
            $this->getUnzerApiFacade(),
            $this->createUnzerGetPaymentMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMapperInterface
     */
    public function createUnzerGetPaymentMapper(): UnzerGetPaymentMapperInterface
    {
        return new UnzerGetPaymentMapper();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface
     */
    public function createUnzerRefundAdapter(): UnzerRefundAdapterInterface
    {
        return new UnzerRefundAdapter(
            $this->getUnzerApiFacade(),
            $this->createUnzerRefundMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerRefundMapperInterface
     */
    public function createUnzerRefundMapper(): UnzerRefundMapperInterface
    {
        return new UnzerRefundMapper();
    }

    /**
     * @return \SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface
     */
    public function getUnzerApiFacade(): UnzerApiFacadeInterface
    {
        /** @var \SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface $unzerApiFacade */
        $unzerApiFacade = $this->getProvidedDependency(UnzerDependencyProvider::FACADE_UNZER_API);

        return $unzerApiFacade;
    }

    /**
     * @return \Spryker\Client\Quote\QuoteClientInterface
     */
    public function getQuoteClient(): QuoteClientInterface
    {
        /** @var \Spryker\Client\Quote\QuoteClientInterface $quoteClient */
        $quoteClient = $this->getProvidedDependency(UnzerDependencyProvider::CLIENT_QUOTE);

        return $quoteClient;
    }

    /**
     * @return \Spryker\Zed\Refund\Business\RefundFacadeInterface|null
     */
    public function getRefundFacade(): ?RefundFacadeInterface
    {
        /** @var \Spryker\Zed\Refund\Business\RefundFacade $refundFacade */
        $refundFacade = $this->getProvidedDependency(UnzerDependencyProvider::FACADE_REFUND);

        return $refundFacade;
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Notification\NotificationProcessorInterface
     */
    public function createUnzerNotificationProcessor(): NotificationProcessorInterface
    {
        return new NotificationProcessor(
            $this->createUnzerPaymentAdapter(),
            $this->getConfig(),
            $this->createUnzerReader(),
            $this->createUnzerPaymentMapper(),
            $this->createUnzerPaymentSaver()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsAuthorizePendingOmsCondition(): UnzerConditionInterface
    {
        return new IsAuthorizePendingOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsAuthorizeSucceededOmsCondition(): UnzerConditionInterface
    {
        return new IsAuthorizeSucceededOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsAuthorizeFailedOmsCondition(): UnzerConditionInterface
    {
        return new IsAuthorizeFailedOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsAuthorizeCanceledOmsCondition(): UnzerConditionInterface
    {
        return new IsAuthorizeCanceledOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsPaymentCompletedOmsCondition(): UnzerConditionInterface
    {
        return new IsPaymentCompletedOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsChargeFailedOmsCondition(): UnzerConditionInterface
    {
        return new IsChargeFailedOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsChargebackOmsCondition(): UnzerConditionInterface
    {
        return new IsChargebackOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Command\UnzerOmsCommandByOrderInterface
     */
    public function createChargeOmsCommand(): UnzerOmsCommandByOrderInterface
    {
        return new ChargeOmsCommand(
            $this->createPaymentProcessorStrategyResolver()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Filter\UnzerPaymentMethodFilterInterface
     */
    public function createPaymentMethodFilter(): UnzerPaymentMethodFilterInterface
    {
        return new UnzerPaymentMethodFilter($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Command\UnzerRefundOmsCommandByOrderInterface
     */
    public function createRefundOmsCommand(): UnzerRefundOmsCommandByOrderInterface
    {
        return new RefundOmsCommand(
            $this->getRefundFacade(),
            $this->createPaymentProcessorStrategyResolver()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface
     */
    public function createUnzerPaymentMapper(): UnzerPaymentMapperInterface
    {
        return new UnzerPaymentMapper();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorStrategyResolverInterface
     */
    public function createPaymentProcessorStrategyResolver(): UnzerPaymentProcessorStrategyResolverInterface
    {
        $strategyContainer = [
            UnzerConfig::PAYMENT_METHOD_MARKETPLACE_BANK_TRANSFER => function () {
                return $this->createMarketplaceBankTransferPaymentProcessor();
            },
            UnzerConfig::PAYMENT_METHOD_MARKETPLACE_CREDIT_CARD => function () {
                return $this->createMarketplaceCreditCardPaymentProcessor();
            },
            UnzerConfig::PAYMENT_METHOD_MARKETPLACE_SOFORT => function () {
                return $this->createMarketplaceBankTransferPaymentProcessor();
            },
        ];

        return new UnzerPaymentProcessorStrategyResolver($strategyContainer);
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface
     */
    public function createMarketplaceBankTransferPaymentProcessor(): UnzerPaymentProcessorInterface
    {
        return new MarketplaceBankTransferProcessor(
            $this->createUnzerCheckoutHookMapper(),
            $this->createUnzerBasketAdapter(),
            $this->createUnzerChargeAdapter(),
            $this->createUnzerPaymentResourceAdapter(),
            $this->createUnzerMarketplaceRefundProcessor()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface
     */
    public function createMarketplaceCreditCardPaymentProcessor(): UnzerPaymentProcessorInterface
    {
        return new MarketplaceCreditCardProcessor(
            $this->createUnzerCheckoutHookMapper(),
            $this->createUnzerBasketAdapter(),
            $this->createUnzerAuthorizeAdapter(),
            $this->createUnzerPaymentAdapter(),
            $this->createUnzerMarketplaceCreditCardChargeProcessor(),
            $this->createUnzerMarketplaceRefundProcessor()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerChargeProcessorInterface
     */
    public function createUnzerMarketplaceCreditCardChargeProcessor(): UnzerChargeProcessorInterface
    {
        return new UnzerMarketplaceCreditCardChargeProcessor(
            $this->createUnzerReader(),
            $this->createUnzerPaymentMapper(),
            $this->createUnzerChargeAdapter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface
     */
    public function createUnzerMarketplaceRefundProcessor(): UnzerRefundProcessorInterface
    {
        return new MarketplaceRefundProcessor(
            $this->createUnzerReader(),
            $this->createUnzerRefundAdapter(),
            $this->createUnzerPaymentMapper(),
            $this->createUnzerPaymentAdapter(),
            $this->createUnzerPaymentSaver()
        );
    }
}
