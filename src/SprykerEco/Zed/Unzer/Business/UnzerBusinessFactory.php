<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
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
use SprykerEco\Zed\Unzer\Business\Notification\UnzerNotificationProcessor;
use SprykerEco\Zed\Unzer\Business\Notification\UnzerNotificationProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Oms\Command\ChargeOmsCommand;
use SprykerEco\Zed\Unzer\Business\Oms\Command\RefundOmsCommand;
use SprykerEco\Zed\Unzer\Business\Oms\Command\UnzerOmsCommandInterface;
use SprykerEco\Zed\Unzer\Business\Oms\Command\UnzerRefundOmsCommandInterface;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsAuthorizeCanceledOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsAuthorizeFailedOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsAuthorizePendingOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsAuthorizeSucceededOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsChargebackOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsChargeFailedOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsPaymentCompletedOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Filter\UnzerMarketplacePaymentMethodFilter;
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
use SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolver;
use SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaver;
use SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface;
use SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteMapper;
use SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteMapperInterface;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerQuoteExpander;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerQuoteExpanderInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReader;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerWriter;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToQuoteClientInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToRefundFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;
use SprykerEco\Zed\Unzer\UnzerDependencyProvider;

/**
 * @method \SprykerEco\Zed\Unzer\UnzerConfig getConfig()
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
            $this->createUnzerPaymentSaver(),
            $this->createPaymentProcessorResolver()
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
            $this->getRepository()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteMapperInterface
     */
    public function createUnzerQuoteExpanderMapper(): UnzerQuoteMapperInterface
    {
        return new UnzerQuoteMapper();
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
            $this->createUnzerAuthorizePaymentMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerAuthorizePaymentMapperInterface
     */
    public function createUnzerAuthorizePaymentMapper(): UnzerAuthorizePaymentMapperInterface
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
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    public function getUnzerApiFacade(): UnzerToUnzerApiFacadeInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::FACADE_UNZER_API);
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToQuoteClientInterface
     */
    public function getQuoteClient(): UnzerToQuoteClientInterface
    {
        /** @var \SprykerEco\Zed\Unzer\Dependency\UnzerToQuoteClientInterface $quoteClient */
        $quoteClient = $this->getProvidedDependency(UnzerDependencyProvider::CLIENT_QUOTE);

        return $quoteClient;
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToRefundFacadeInterface
     */
    public function getRefundFacade(): UnzerToRefundFacadeInterface
    {
        /** @var \SprykerEco\Zed\Unzer\Dependency\UnzerToRefundFacadeInterface $refundFacade */
        $refundFacade = $this->getProvidedDependency(UnzerDependencyProvider::FACADE_REFUND);

        return $refundFacade;
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Notification\UnzerNotificationProcessorInterface
     */
    public function createUnzerNotificationProcessor(): UnzerNotificationProcessorInterface
    {
        return new UnzerNotificationProcessor(
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
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Command\UnzerOmsCommandInterface
     */
    public function createChargeOmsCommand(): UnzerOmsCommandInterface
    {
        return new ChargeOmsCommand(
            $this->createPaymentProcessorResolver()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Filter\UnzerPaymentMethodFilterInterface
     */
    public function createMarketplacePaymentMethodFilter(): UnzerPaymentMethodFilterInterface
    {
        return new UnzerMarketplacePaymentMethodFilter($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Command\UnzerRefundOmsCommandInterface
     */
    public function createRefundOmsCommand(): UnzerRefundOmsCommandInterface
    {
        return new RefundOmsCommand(
            $this->getRefundFacade(),
            $this->createPaymentProcessorResolver()
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
     * @return \SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface
     */
    public function createPaymentProcessorResolver(): UnzerPaymentProcessorResolverInterface
    {
        $unzerPaymentProcessorsCollection = [
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

        return new UnzerPaymentProcessorResolver($unzerPaymentProcessorsCollection);
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
