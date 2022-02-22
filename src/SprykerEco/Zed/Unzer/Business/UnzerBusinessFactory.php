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
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMethodsMapper;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMethodsMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerMetadataMapper;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerMetadataMapperInterface;
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
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerMetadataAdapter;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerMetadataAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerNotificationAdapter;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerNotificationAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapter;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapter;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentResourceAdapter;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentResourceAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapter;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutPostSaveMapper;
use SprykerEco\Zed\Unzer\Business\Checkout\UnzerCheckoutHookInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\UnzerPostSaveCheckoutHook;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsCreator;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsCreatorInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsDeleter;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsDeleterInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolver;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsStoreRelationUpdater;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsStoreRelationUpdaterInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsUpdater;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsUpdaterInterface;
use SprykerEco\Zed\Unzer\Business\Import\Adapter\PaymentImportAdapter;
use SprykerEco\Zed\Unzer\Business\Import\Adapter\PaymentImportAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Import\Filter\UnzerPaymentMethodImportFilter;
use SprykerEco\Zed\Unzer\Business\Import\Filter\UnzerPaymentMethodImportFilterInterface;
use SprykerEco\Zed\Unzer\Business\Import\UnzerPaymentMethodImporter;
use SprykerEco\Zed\Unzer\Business\Import\UnzerPaymentMethodImporterInterface;
use SprykerEco\Zed\Unzer\Business\Notification\Configurator\UnzerNotificationConfigurator;
use SprykerEco\Zed\Unzer\Business\Notification\Configurator\UnzerNotificationConfiguratorInterface;
use SprykerEco\Zed\Unzer\Business\Notification\Processor\UnzerNotificationProcessor;
use SprykerEco\Zed\Unzer\Business\Notification\Processor\UnzerNotificationProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Oms\Command\ChargeUnzerOmsCommand;
use SprykerEco\Zed\Unzer\Business\Oms\Command\RefundUnzerOmsCommand;
use SprykerEco\Zed\Unzer\Business\Oms\Command\RefundUnzerOmsCommandInterface;
use SprykerEco\Zed\Unzer\Business\Oms\Command\UnzerOmsCommandInterface;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsAuthorizeCanceledOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsAuthorizeFailedOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsAuthorizePendingOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsAuthorizeSucceededOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsChargebackOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsChargeFailedOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\IsPaymentCompletedOmsCondition;
use SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Filter\UnzerIntersectionPaymentMethodFilter;
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
use SprykerEco\Zed\Unzer\Business\Quote\UnzerCustomerQuoteExpander;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerCustomerQuoteExpanderInterface;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerKeypairQuoteExpander;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerKeypairQuoteExpanderInterface;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerMetadataQuoteExpander;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerMetadataQuoteExpanderInterface;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerQuoteExpander;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerQuoteExpanderInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReader;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerVaultReader;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerVaultReaderInterface;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriter;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriterInterface;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerWriter;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToLocaleFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToPaymentFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToQuoteClientInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToRefundFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUtilTextServiceInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToVaultFacadeInterface;
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
            $this->createUnzerCustomerQuoteExpander(),
            $this->createUnzerMetadataQuoteExpander(),
            $this->createUnzerKeypairQuoteExpander(),
            $this->getQuoteClient(),
            $this->getConfig(),
            $this->createUnzerReader(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Checkout\UnzerCheckoutHookInterface
     */
    public function createUnzerPostSaveCheckoutHook(): UnzerCheckoutHookInterface
    {
        return new UnzerPostSaveCheckoutHook(
            $this->createUnzerPaymentSaver(),
            $this->createPaymentProcessorResolver(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface
     */
    public function createUnzerCheckoutHookMapper(): UnzerCheckoutMapperInterface
    {
        return new UnzerCheckoutPostSaveMapper(
            $this->getConfig(),
            $this->getUtilTextService(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    public function createUnzerReader(): UnzerReaderInterface
    {
        return new UnzerReader(
            $this->getRepository(),
            $this->createUnzerVaultReader(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteMapperInterface
     */
    public function createUnzerQuoteMapper(): UnzerQuoteMapperInterface
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
            $this->createUnzerReader(),
            $this->getConfig(),
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
            $this->createUnzerPaymentMapper(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface
     */
    public function createUnzerCustomerAdapter(): UnzerCustomerAdapterInterface
    {
        return new UnzerCustomerAdapter(
            $this->getUnzerApiFacade(),
            $this->createUnzerCustomerMapper(),
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
            $this->createUnzerBasketMapper(),
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
            $this->createUnzerPaymentResourceMapper(),
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
            $this->createUnzerAuthorizePaymentMapper(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerAuthorizePaymentMapperInterface
     */
    public function createUnzerAuthorizePaymentMapper(): UnzerAuthorizePaymentMapperInterface
    {
        return new UnzerAuthorizePaymentMapper(
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerChargeAdapterInterface
     */
    public function createUnzerChargeAdapter(): UnzerChargeAdapterInterface
    {
        return new UnzerChargeAdapter(
            $this->getUnzerApiFacade(),
            $this->createUnzerChargeMapper(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerChargeMapperInterface
     */
    public function createUnzerChargeMapper(): UnzerChargeMapperInterface
    {
        return new UnzerChargeMapper(
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface
     */
    public function createUnzerPaymentAdapter(): UnzerPaymentAdapterInterface
    {
        return new UnzerPaymentAdapter(
            $this->getUnzerApiFacade(),
            $this->createUnzerGetPaymentMapper(),
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
            $this->createUnzerRefundMapper(),
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
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToPaymentFacadeInterface
     */
    public function getPaymentFacade(): UnzerToPaymentFacadeInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::FACADE_PAYMENT);
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
     * @return \SprykerEco\Zed\Unzer\Business\Notification\Processor\UnzerNotificationProcessorInterface
     */
    public function createUnzerNotificationProcessor(): UnzerNotificationProcessorInterface
    {
        return new UnzerNotificationProcessor(
            $this->createUnzerPaymentAdapter(),
            $this->getConfig(),
            $this->createUnzerReader(),
            $this->createUnzerPaymentMapper(),
            $this->createUnzerPaymentSaver(),
            $this->createUnzerCredentialsResolver(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsAuthorizePendingOmsCondition(): UnzerConditionInterface
    {
        return new IsAuthorizePendingOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsAuthorizeSucceededOmsCondition(): UnzerConditionInterface
    {
        return new IsAuthorizeSucceededOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsAuthorizeFailedOmsCondition(): UnzerConditionInterface
    {
        return new IsAuthorizeFailedOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsAuthorizeCanceledOmsCondition(): UnzerConditionInterface
    {
        return new IsAuthorizeCanceledOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsPaymentCompletedOmsCondition(): UnzerConditionInterface
    {
        return new IsPaymentCompletedOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsChargeFailedOmsCondition(): UnzerConditionInterface
    {
        return new IsChargeFailedOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Condition\UnzerConditionInterface
     */
    public function createIsChargebackOmsCondition(): UnzerConditionInterface
    {
        return new IsChargebackOmsCondition(
            $this->createUnzerReader(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Command\UnzerOmsCommandInterface
     */
    public function createChargeOmsCommand(): UnzerOmsCommandInterface
    {
        return new ChargeUnzerOmsCommand(
            $this->createPaymentProcessorResolver(),
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
     * @return \SprykerEco\Zed\Unzer\Business\Oms\Command\RefundUnzerOmsCommandInterface
     */
    public function createRefundOmsCommand(): RefundUnzerOmsCommandInterface
    {
        return new RefundUnzerOmsCommand(
            $this->getRefundFacade(),
            $this->createPaymentProcessorResolver(),
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
            UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_BANK_TRANSFER => function () {
                return $this->createMarketplaceBankTransferPaymentProcessor();
            },
            UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_CREDIT_CARD => function () {
                return $this->createMarketplaceCreditCardPaymentProcessor();
            },
            UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_SOFORT => function () {
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
            $this->createUnzerMarketplaceRefundProcessor(),
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
            $this->createUnzerMarketplaceRefundProcessor(),
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
            $this->createUnzerChargeAdapter(),
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
            $this->createUnzerPaymentSaver(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Quote\UnzerCustomerQuoteExpanderInterface
     */
    public function createUnzerCustomerQuoteExpander(): UnzerCustomerQuoteExpanderInterface
    {
        return new UnzerCustomerQuoteExpander(
            $this->createUnzerCustomerAdapter(),
            $this->createUnzerCustomerMapper(),
            $this->createUnzerQuoteMapper(),
            $this->createUnzerReader(),
            $this->createUnzerWriter(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Quote\UnzerMetadataQuoteExpanderInterface
     */
    public function createUnzerMetadataQuoteExpander(): UnzerMetadataQuoteExpanderInterface
    {
        return new UnzerMetadataQuoteExpander(
            $this->createUnzerMetadataAdapter(),
            $this->getLocaleFacade(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerMetadataAdapterInterface
     */
    public function createUnzerMetadataAdapter(): UnzerMetadataAdapterInterface
    {
        return new UnzerMetadataAdapter(
            $this->getUnzerApiFacade(),
            $this->createUnzerMetadataMapper(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToLocaleFacadeInterface
     */
    public function getLocaleFacade(): UnzerToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerMetadataMapperInterface
     */
    public function createUnzerMetadataMapper(): UnzerMetadataMapperInterface
    {
        return new UnzerMetadataMapper();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Notification\Configurator\UnzerNotificationConfiguratorInterface
     */
    public function createUnzerNotificationConfigurator(): UnzerNotificationConfiguratorInterface
    {
        return new UnzerNotificationConfigurator(
            $this->getConfig(),
            $this->createUnzerNotificationAdapter(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Quote\UnzerKeypairQuoteExpanderInterface
     */
    public function createUnzerKeypairQuoteExpander(): UnzerKeypairQuoteExpanderInterface
    {
        return new UnzerKeypairQuoteExpander(
            $this->createUnzerCredentialsResolver(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerNotificationAdapterInterface
     */
    public function createUnzerNotificationAdapter(): UnzerNotificationAdapterInterface
    {
        return new UnzerNotificationAdapter($this->getUnzerApiFacade());
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToVaultFacadeInterface
     */
    public function getVaultFacade(): UnzerToVaultFacadeInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::FACADE_VAULT);
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsCreatorInterface
     */
    public function createUnzerCredentialsCreator(): UnzerCredentialsCreatorInterface
    {
        return new UnzerCredentialsCreator(
            $this->getEntityManager(),
            $this->createUnzerCredentialsStoreRelationUpdater(),
            $this->createUnzerVaultWriter(),
            $this->getUtilTextService(),
            $this->createUnzerNotificationConfigurator(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsUpdaterInterface
     */
    public function createUnzerCredentialsUpdater(): UnzerCredentialsUpdaterInterface
    {
        return new UnzerCredentialsUpdater(
            $this->getEntityManager(),
            $this->createUnzerCredentialsStoreRelationUpdater(),
            $this->createUnzerVaultWriter(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsStoreRelationUpdaterInterface
     */
    public function createUnzerCredentialsStoreRelationUpdater(): UnzerCredentialsStoreRelationUpdaterInterface
    {
        return new UnzerCredentialsStoreRelationUpdater(
            $this->getEntityManager(),
            $this->getRepository(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Reader\UnzerVaultReaderInterface
     */
    public function createUnzerVaultReader(): UnzerVaultReaderInterface
    {
        return new UnzerVaultReader(
            $this->getVaultFacade(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriterInterface
     */
    public function createUnzerVaultWriter(): UnzerVaultWriterInterface
    {
        return new UnzerVaultWriter(
            $this->getVaultFacade(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface
     */
    public function createUnzerCredentialsResolver(): UnzerCredentialsResolverInterface
    {
        return new UnzerCredentialsResolver($this->createUnzerReader());
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToUtilTextServiceInterface
     */
    public function getUtilTextService(): UnzerToUtilTextServiceInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::SERVICE_UTIL_TEXT);
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Import\UnzerPaymentMethodImporterInterface
     */
    public function createUnzerPaymentMethodsImporter(): UnzerPaymentMethodImporterInterface
    {
        return new UnzerPaymentMethodImporter(
            $this->getConfig(),
            $this->createUnzerPaymentMethodImportFilter(),
            $this->createPaymentImportAdapter(),
            $this->createUnzerPaymentMethodsAdapter(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Import\Adapter\PaymentImportAdapterInterface
     */
    public function createPaymentImportAdapter(): PaymentImportAdapterInterface
    {
        return new PaymentImportAdapter($this->getPaymentFacade());
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Import\Filter\UnzerPaymentMethodImportFilterInterface
     */
    public function createUnzerPaymentMethodImportFilter(): UnzerPaymentMethodImportFilterInterface
    {
        return new UnzerPaymentMethodImportFilter();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Filter\UnzerPaymentMethodFilterInterface
     */
    public function createUnzerIntersectionPaymentMethodFilter(): UnzerPaymentMethodFilterInterface
    {
        return new UnzerIntersectionPaymentMethodFilter(
            $this->getConfig(),
            $this->createUnzerReader(),
            $this->createUnzerPaymentMethodsAdapter(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMethodsMapperInterface
     */
    public function createUnzerGetPaymentMethodsMapper(): UnzerGetPaymentMethodsMapperInterface
    {
        return new UnzerGetPaymentMethodsMapper($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface
     */
    public function createUnzerPaymentMethodsAdapter(): UnzerPaymentMethodsAdapterInterface
    {
        return new UnzerPaymentMethodsAdapter(
            $this->getUnzerApiFacade(),
            $this->createUnzerGetPaymentMethodsMapper(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsDeleterInterface
     */
    public function createUnzerCredentialsEraser(): UnzerCredentialsDeleterInterface
    {
        return new UnzerCredentialsDeleter(
            $this->getRepository(),
            $this->getEntityManager(),
        );
    }
}
