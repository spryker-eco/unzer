<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Shared\Unzer\UnzerConfig;
use SprykerEco\Shared\Unzer\UnzerConstants;
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
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidator;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\ExpenseDistributor\UnzerExpenseDistributor;
use SprykerEco\Zed\Unzer\Business\Checkout\ExpenseDistributor\UnzerExpenseDistributorInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapper;
use SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\UnzerCheckoutHookExecutorInterface;
use SprykerEco\Zed\Unzer\Business\Checkout\UnzerPostSaveCheckoutHookExecutor;
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
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsMerchantReferenceValidator;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsParentCredentialsValidator;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsStoreRelationValidator;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsUniqueMerchantReferenceValidator;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsUniquePublicKeyValidator;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsValidatorComposite;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsValidatorInterface;
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
use SprykerEco\Zed\Unzer\Business\Payment\Filter\UnzerEnabledPaymentMethodFilter;
use SprykerEco\Zed\Unzer\Business\Payment\Filter\UnzerMarketplacePaymentMethodFilter;
use SprykerEco\Zed\Unzer\Business\Payment\Filter\UnzerPaymentMethodFilterInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapper;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerChargeProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerCreditCardChargeProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerMarketplaceCreditCardChargeProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\CreditCardProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\DirectCharge\UnzerDirectChargeProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\DirectCharge\UnzerDirectChargeProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\DirectPaymentProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\MarketplaceCreditCardProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\MarketplaceDirectPaymentProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment\UnzerPreparePaymentProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment\UnzerPreparePaymentProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Mapper\UnzerMarketplaceRefundMapper;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Mapper\UnzerMarketplaceRefundMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Saver\UnzerRefundPaymentSaver;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Saver\UnzerRefundPaymentSaverInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerMarketplaceRefundProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessor;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface;
use SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolver;
use SprykerEco\Zed\Unzer\Business\Payment\ProcessorResolver\UnzerPaymentProcessorResolverInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Reader\UnzerPaymentReader;
use SprykerEco\Zed\Unzer\Business\Payment\Reader\UnzerPaymentReaderInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdater;
use SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface;
use SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteMapper;
use SprykerEco\Zed\Unzer\Business\Quote\Mapper\UnzerQuoteMapperInterface;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerCustomerQuoteExpander;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerCustomerQuoteExpanderInterface;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerKeypairQuoteExpander;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerKeypairQuoteExpanderInterface;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerMetadataQuoteExpander;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerMetadataQuoteExpanderInterface;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerParticipantIdQuoteExpander;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerParticipantIdQuoteExpanderInterface;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerQuoteExpander;
use SprykerEco\Zed\Unzer\Business\Quote\UnzerQuoteExpanderInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReader;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerVaultReader;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerVaultReaderInterface;
use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpenseRefundStrategyResolver\UnzerExpenseRefundStrategyResolver;
use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpenseRefundStrategyResolver\UnzerExpenseRefundStrategyResolverInterface;
use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\LastOrderItemExpenseRefundStrategy;
use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\LastShipmentItemExpenseRefundStrategy;
use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\NoExpenseRefundStrategy;
use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpenseRefundStrategyInterface;
use SprykerEco\Zed\Unzer\Business\Refund\UnzerRefundExpander;
use SprykerEco\Zed\Unzer\Business\Refund\UnzerRefundExpanderInterface;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriter;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriterInterface;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerWriter;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToLocaleFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToPaymentFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToRefundFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface;
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
            $this->createUnzerParticipantIdQuoteExpander(),
            $this->getConfig(),
            $this->createUnzerReader(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Checkout\UnzerCheckoutHookExecutorInterface
     */
    public function createUnzerPostSaveCheckoutHook(): UnzerCheckoutHookExecutorInterface
    {
        return new UnzerPostSaveCheckoutHookExecutor(
            $this->createUnzerPaymentUpdater(),
            $this->createUnzerPaymentProcessorResolver(),
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
        return new UnzerQuoteMapper($this->getUtilTextService());
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
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface
     */
    public function createUnzerPaymentUpdater(): UnzerPaymentUpdaterInterface
    {
        return new UnzerPaymentUpdater(
            $this->createUnzerReader(),
            $this->createUnzerWriter(),
            $this->createUnzerPaymentMapper(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface
     */
    public function createUnzerApiAdapterResponseValidator(): UnzerApiAdapterResponseValidatorInterface
    {
        return new UnzerApiAdapterResponseValidator();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerCustomerAdapterInterface
     */
    public function createUnzerCustomerAdapter(): UnzerCustomerAdapterInterface
    {
        return new UnzerCustomerAdapter(
            $this->getUnzerApiFacade(),
            $this->createUnzerCustomerMapper(),
            $this->createUnzerApiAdapterResponseValidator(),
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
            $this->createUnzerApiAdapterResponseValidator(),
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
            $this->createUnzerApiAdapterResponseValidator(),
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
            $this->createUnzerApiAdapterResponseValidator(),
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
            $this->createUnzerApiAdapterResponseValidator(),
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
            $this->createUnzerApiAdapterResponseValidator(),
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
            $this->createUnzerApiAdapterResponseValidator(),
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
     * @return \SprykerEco\Zed\Unzer\Business\Notification\Processor\UnzerNotificationProcessorInterface
     */
    public function createUnzerNotificationProcessor(): UnzerNotificationProcessorInterface
    {
        return new UnzerNotificationProcessor(
            $this->createUnzerPaymentAdapter(),
            $this->getConfig(),
            $this->createUnzerReader(),
            $this->createUnzerPaymentMapper(),
            $this->createUnzerPaymentUpdater(),
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
    public function createChargeUnzerOmsCommand(): UnzerOmsCommandInterface
    {
        return new ChargeUnzerOmsCommand(
            $this->createUnzerPaymentProcessorResolver(),
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
    public function createRefundUnzerOmsCommand(): RefundUnzerOmsCommandInterface
    {
        return new RefundUnzerOmsCommand(
            $this->getRefundFacade(),
            $this->createUnzerPaymentProcessorResolver(),
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
    public function createUnzerPaymentProcessorResolver(): UnzerPaymentProcessorResolverInterface
    {
        return new UnzerPaymentProcessorResolver($this->getUnzerPaymentProcessors());
    }

    /**
     * @return array<string, \Closure>
     */
    public function getUnzerPaymentProcessors(): array
    {
        return [
            UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_BANK_TRANSFER => function () {
                return $this->createMarketplaceDirectPaymentProcessor();
            },
            UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_CREDIT_CARD => function () {
                return $this->createMarketplaceCreditCardProcessor();
            },
            UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_SOFORT => function () {
                return $this->createMarketplaceDirectPaymentProcessor();
            },
            UnzerConfig::PAYMENT_METHOD_KEY_BANK_TRANSFER => function () {
                return $this->createDirectPaymentProcessor();
            },
            UnzerConfig::PAYMENT_METHOD_KEY_CREDIT_CARD => function () {
                return $this->createCreditCardProcessor();
            },
            UnzerConfig::PAYMENT_METHOD_KEY_SOFORT => function () {
                return $this->createDirectPaymentProcessor();
            },
        ];
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface
     */
    public function createMarketplaceDirectPaymentProcessor(): UnzerPaymentProcessorInterface
    {
        return new MarketplaceDirectPaymentProcessor(
            $this->createUnzerPaymentAdapter(),
            $this->createUnzerPaymentResourceAdapter(),
            $this->createUnzerMarketplaceRefundProcessor(),
            $this->createUnzerPreparePaymentProcessor(),
            $this->createUnzerCheckoutMapper(),
            $this->createUnzerDirectChargeProcessor(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface
     */
    public function createMarketplaceCreditCardProcessor(): UnzerPaymentProcessorInterface
    {
        return new MarketplaceCreditCardProcessor(
            $this->createUnzerAuthorizeAdapter(),
            $this->createUnzerPaymentAdapter(),
            $this->createUnzerMarketplaceCreditCardChargeProcessor(),
            $this->createUnzerMarketplaceRefundProcessor(),
            $this->createUnzerPreparePaymentProcessor(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface
     */
    public function createCreditCardProcessor(): UnzerPaymentProcessorInterface
    {
        return new CreditCardProcessor(
            $this->createUnzerAuthorizeAdapter(),
            $this->createUnzerPaymentAdapter(),
            $this->createUnzerCreditCardChargeProcessor(),
            $this->createUnzerRefundProcessor(),
            $this->createUnzerPreparePaymentProcessor(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerPaymentProcessorInterface
     */
    public function createDirectPaymentProcessor(): UnzerPaymentProcessorInterface
    {
        return new DirectPaymentProcessor(
            $this->createUnzerPaymentAdapter(),
            $this->createUnzerPaymentResourceAdapter(),
            $this->createUnzerRefundProcessor(),
            $this->createUnzerPreparePaymentProcessor(),
            $this->createUnzerCheckoutMapper(),
            $this->createUnzerDirectChargeProcessor(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerChargeProcessorInterface
     */
    public function createUnzerCreditCardChargeProcessor(): UnzerChargeProcessorInterface
    {
        return new UnzerCreditCardChargeProcessor(
            $this->createUnzerPaymentMapper(),
            $this->createUnzerChargeAdapter(),
            $this->createUnzerCredentialsResolver(),
            $this->getRepository(),
            $this->getEntityManager(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge\UnzerChargeProcessorInterface
     */
    public function createUnzerMarketplaceCreditCardChargeProcessor(): UnzerChargeProcessorInterface
    {
        return new UnzerMarketplaceCreditCardChargeProcessor(
            $this->createUnzerPaymentMapper(),
            $this->createUnzerChargeAdapter(),
            $this->createUnzerCredentialsResolver(),
            $this->getRepository(),
            $this->getEntityManager(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Checkout\Mapper\UnzerCheckoutMapperInterface
     */
    public function createUnzerCheckoutMapper(): UnzerCheckoutMapperInterface
    {
        return new UnzerCheckoutMapper(
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\PreparePayment\UnzerPreparePaymentProcessorInterface
     */
    public function createUnzerPreparePaymentProcessor(): UnzerPreparePaymentProcessorInterface
    {
        return new UnzerPreparePaymentProcessor(
            $this->createUnzerCheckoutMapper(),
            $this->createUnzerBasketAdapter(),
            $this->createUnzerExpensesDistributor(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Checkout\ExpenseDistributor\UnzerExpenseDistributorInterface
     */
    public function createUnzerExpensesDistributor(): UnzerExpenseDistributorInterface
    {
        return new UnzerExpenseDistributor();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Quote\UnzerParticipantIdQuoteExpanderInterface
     */
    public function createUnzerParticipantIdQuoteExpander(): UnzerParticipantIdQuoteExpanderInterface
    {
        return new UnzerParticipantIdQuoteExpander($this->createUnzerReader());
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface
     */
    public function createUnzerRefundProcessor(): UnzerRefundProcessorInterface
    {
        return new UnzerRefundProcessor(
            $this->createUnzerCredentialsResolver(),
            $this->createUnzerExpenseRefundStrategyResolver(),
            $this->createUnzerRefundAdapter(),
            $this->getRepository(),
            $this->createUnzerRefundPaymentSaver(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\UnzerRefundProcessorInterface
     */
    public function createUnzerMarketplaceRefundProcessor(): UnzerRefundProcessorInterface
    {
        return new UnzerMarketplaceRefundProcessor(
            $this->createUnzerCredentialsResolver(),
            $this->createUnzerExpenseRefundStrategyResolver(),
            $this->createUnzerRefundAdapter(),
            $this->getRepository(),
            $this->createUnzerMarketplaceRefundMapper(),
            $this->createUnzerRefundPaymentSaver(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpenseRefundStrategyResolver\UnzerExpenseRefundStrategyResolverInterface
     */
    public function createUnzerExpenseRefundStrategyResolver(): UnzerExpenseRefundStrategyResolverInterface
    {
        return new UnzerExpenseRefundStrategyResolver(
            $this->getUnzerExpenseRefundStrategies(),
            $this->getConfig(),
        );
    }

    /**
     * @return array<int, \Closure>
     */
    public function getUnzerExpenseRefundStrategies(): array
    {
        return [
            UnzerConstants::NO_EXPENSES_REFUND_STRATEGY => function () {
                return $this->createNoExpenseRefundStrategy();
            },
            UnzerConstants::LAST_SHIPMENT_ITEM_EXPENSES_REFUND_STRATEGY => function () {
                return $this->createLastShipmentItemExpenseRefundStrategy();
            },
            UnzerConstants::LAST_ORDER_ITEM_EXPENSES_REFUND_STRATEGY => function () {
                return $this->createLastOrderItemExpenseRefundStrategy();
            },
        ];
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpenseRefundStrategyInterface
     */
    public function createNoExpenseRefundStrategy(): UnzerExpenseRefundStrategyInterface
    {
        return new NoExpenseRefundStrategy();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpenseRefundStrategyInterface
     */
    public function createLastShipmentItemExpenseRefundStrategy(): UnzerExpenseRefundStrategyInterface
    {
        return new LastShipmentItemExpenseRefundStrategy(
            $this->getRepository(),
            $this->createUnzerRefundExpander(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\UnzerExpenseRefundStrategyInterface
     */
    public function createLastOrderItemExpenseRefundStrategy(): UnzerExpenseRefundStrategyInterface
    {
        return new LastOrderItemExpenseRefundStrategy(
            $this->getRepository(),
            $this->createUnzerRefundExpander(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Refund\UnzerRefundExpanderInterface
     */
    public function createUnzerRefundExpander(): UnzerRefundExpanderInterface
    {
        return new UnzerRefundExpander(
            $this->createUnzerReader(),
            $this->getRepository(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Mapper\UnzerMarketplaceRefundMapperInterface
     */
    public function createUnzerMarketplaceRefundMapper(): UnzerMarketplaceRefundMapperInterface
    {
        return new UnzerMarketplaceRefundMapper();
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
            $this->createUnzerApiAdapterResponseValidator(),
        );
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
            $this->createUnzerReader(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerNotificationAdapterInterface
     */
    public function createUnzerNotificationAdapter(): UnzerNotificationAdapterInterface
    {
        return new UnzerNotificationAdapter(
            $this->getUnzerApiFacade(),
            $this->createUnzerApiAdapterResponseValidator(),
        );
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
            $this->createUnzerReader(),
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
            $this->createUnzerReader(),
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
     * @return \SprykerEco\Zed\Unzer\Business\Import\UnzerPaymentMethodImporterInterface
     */
    public function createUnzerPaymentMethodImporter(): UnzerPaymentMethodImporterInterface
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
    public function createUnzerEnabledPaymentMethodFilter(): UnzerPaymentMethodFilterInterface
    {
        return new UnzerEnabledPaymentMethodFilter(
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
            $this->createUnzerApiAdapterResponseValidator(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToUtilTextServiceInterface
     */
    public function getUtilTextService(): UnzerToUtilTextServiceInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::SERVICE_UTIL_TEXT);
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToVaultFacadeInterface
     */
    public function getVaultFacade(): UnzerToVaultFacadeInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::FACADE_VAULT);
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToLocaleFacadeInterface
     */
    public function getLocaleFacade(): UnzerToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface
     */
    public function getSalesFacade(): UnzerToSalesFacadeInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::FACADE_SALES);
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToRefundFacadeInterface
     */
    public function getRefundFacade(): UnzerToRefundFacadeInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::FACADE_REFUND);
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
     * @return \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsDeleterInterface
     */
    public function createUnzerCredentialsDeleter(): UnzerCredentialsDeleterInterface
    {
        return new UnzerCredentialsDeleter(
            $this->getRepository(),
            $this->getEntityManager(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface
     */
    public function getMerchantFacade(): UnzerToMerchantFacadeInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsValidatorInterface
     */
    public function createUnzerCredentialsValidator(): UnzerCredentialsValidatorInterface
    {
        return new UnzerCredentialsValidatorComposite([
            $this->createUnzerCredentialsMerchantReferenceValidator(),
            $this->createUnzerCredentialsUniqueMerchantReferenceValidator(),
            $this->createUnzerCredentialsParentCredentialsValidator(),
            $this->createUnzerCredentialsStoreRelationsValidator(),
            $this->createUnzerCredentialsUniquePublicKeyValidator(),
        ]);
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsValidatorInterface
     */
    public function createUnzerCredentialsMerchantReferenceValidator(): UnzerCredentialsValidatorInterface
    {
        return new UnzerCredentialsMerchantReferenceValidator($this->getMerchantFacade());
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsValidatorInterface
     */
    public function createUnzerCredentialsUniqueMerchantReferenceValidator(): UnzerCredentialsValidatorInterface
    {
        return new UnzerCredentialsUniqueMerchantReferenceValidator($this->createUnzerReader());
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsValidatorInterface
     */
    public function createUnzerCredentialsParentCredentialsValidator(): UnzerCredentialsValidatorInterface
    {
        return new UnzerCredentialsParentCredentialsValidator($this->createUnzerReader());
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsValidatorInterface
     */
    public function createUnzerCredentialsStoreRelationsValidator(): UnzerCredentialsValidatorInterface
    {
        return new UnzerCredentialsStoreRelationValidator($this->createUnzerReader());
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsValidatorInterface
     */
    public function createUnzerCredentialsUniquePublicKeyValidator(): UnzerCredentialsValidatorInterface
    {
        return new UnzerCredentialsUniquePublicKeyValidator($this->createUnzerReader());
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Saver\UnzerRefundPaymentSaverInterface
     */
    public function createUnzerRefundPaymentSaver(): UnzerRefundPaymentSaverInterface
    {
        return new UnzerRefundPaymentSaver(
            $this->createUnzerPaymentMapper(),
            $this->createUnzerPaymentAdapter(),
            $this->createUnzerPaymentUpdater(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Reader\UnzerPaymentReaderInterface
     */
    public function createUnzerPaymentReader(): UnzerPaymentReaderInterface
    {
        return new UnzerPaymentReader(
            $this->createUnzerReader(),
            $this->createUnzerPaymentMapper(),
            $this->createUnzerPaymentAdapter(),
            $this->createUnzerCredentialsResolver(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Processor\DirectCharge\UnzerDirectChargeProcessorInterface
     */
    public function createUnzerDirectChargeProcessor(): UnzerDirectChargeProcessorInterface
    {
        return new UnzerDirectChargeProcessor(
            $this->createUnzerChargeAdapter(),
            $this->createUnzerChargeMapper(),
            $this->getRepository(),
            $this->getEntityManager(),
        );
    }
}
