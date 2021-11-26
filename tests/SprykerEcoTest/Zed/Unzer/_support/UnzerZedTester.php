<?php

namespace SprykerEcoTest\Zed\Unzer;

use Codeception\Actor;
use Codeception\Scenario;
use Generated\Shared\DataBuilder\QuoteBuilder;
use Generated\Shared\DataBuilder\SaveOrderBuilder;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Generated\Shared\Transfer\UnzerAddressTransfer;
use Generated\Shared\Transfer\UnzerApiAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiChargeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreateBasketRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreateBasketResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreateCustomerRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreateCustomerResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreateMetadataRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreateMetadataResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreatePaymentResourceRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreatePaymentResourceResponseTransfer;
use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiMarketplaceRefundRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRefundRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use Generated\Shared\Transfer\UnzerApiSetWebhookRequestTransfer;
use Generated\Shared\Transfer\UnzerApiSetWebhookResponseTransfer;
use Generated\Shared\Transfer\UnzerApiUpdateCustomerRequestTransfer;
use Generated\Shared\Transfer\UnzerApiUpdateCustomerResponseTransfer;
use Generated\Shared\Transfer\UnzerBasketItemTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerNotificationConfigTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as SharedUnzerConfig;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Shared\UnzerApi\UnzerApiConstants;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerBasketAdapter;
use SprykerEco\Zed\Unzer\Business\UnzerBusinessFactory;
use SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToCalculationFacadeBridge;
use SprykerEco\Zed\Unzer\Persistence\UnzerEntityManager;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepository;
use SprykerEco\Zed\Unzer\UnzerConfig;
use SprykerEco\Zed\UnzerApi\Dependency\Service\UnzerApiToUtilEncodingServiceBridge;
use SprykerEco\Zed\UnzerApi\Persistence\UnzerApiEntityManager;
use SprykerEco\Zed\UnzerApi\UnzerApiConfig;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class UnzerZedTester extends Actor
{
    use _generated\UnzerZedTesterActions;

    public const PAYMENT_SELECTION_MARKETPLACE = 'unzerMarketplaceBankTransfer';
    public const TOTALS_PRICE_TO_PAY = 72350;
    public const ADDRESS_COUNTRY = 'Germany';
    public const ADDRESS_CITY = 'Berlin';
    public const ADDRESS_ZIP = '20537';
    public const ADDRESS_ADDITIONAL = 'street';
    public const ADDRESS_NO = 324324;
    public const ADDRESS_STREET = 'Street';
    public const CUSTOMER_SALUTATION = 'Mr';
    public const CUSTOMER_SURNAME = 'Doe';
    public const CUSTOMER_NAME = 'John';
    public const CUSTOMER_EMAIL = 'john.doe@mail.com';
    public const CUSTOMER_REFERENCE = 'DE-22';
    public const PAYMENT_METHOD = 'unzerMarketplaceBankTransfer';
    public const PAYMENT_PROVIDER = 'unzer';

    public const UNZER_API_RESPONSE_CUSTOMER_ID = 's-cust-43434234';
    public const UNZER_API_RESPONSE_METADATA_ID = 's-meta-5423423';
    public const UNZER_API_RESPONSE_BASKET_ID = 's-bskt-435345';
    public const UNZER_API_RESPONSE_PAYMENT_RESOURCE_ID = 's-type-sofort-1';
    public const UNZER_API_RESPONSE_WEBHOOK_URL = 'https://unzer-spryker.com';
    const UNZER_KEYPAIR_ID = 'key-1';
    const UNZER_PUBLIC_KEY = 's-pub';
    const UNZER_PRIVATE_KEY = 's-priv';
    const UNZER_PAYMENT_ID = 's-pay-1234';
    const UNZER_EVENT_AUTHORIZED = 'authorize.succeeded';
    const ORDER_REFERENCE = 'ORD-DE-12';

    /**
     * @param \Codeception\Scenario $scenario
     */
    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);

        $this->setUpConfig();
    }

    /**
     * @return void
     */
    protected function setUpConfig(): void
    {
        $this->setConfig(UnzerConstants::UNZER_AUTHORIZE_RETURN_URL, 'https://spryker.com');
        $this->setConfig(UnzerConstants::UNZER_CHARGE_RETURN_URL, 'https://spryker.com');
        $this->setConfig(UnzerConstants::WEBHOOK_RETRIEVE_URL, 'https://spryker.com');
        $this->setConfig(UnzerConstants::MASTER_MERCHANT_PARTICIPANT_ID, '111111');
        $this->setConfig(UnzerConstants::PRIMARY_KEYPAIR_ID, 'id');
    }

    /**
     * @return UnzerConfig
     */
    public function createConfig(): UnzerConfig
    {
        return new UnzerConfig();
    }

    /**
     * @return UnzerEntityManager
     */
    public function createEntityManager(): UnzerEntityManager
    {
        return new UnzerEntityManager();
    }

    /**
     * @return UnzerRepository
     */
    public function createRepository(): UnzerRepository
    {
        return new UnzerRepository();
    }

    /**
     * @return QuoteTransfer
     */
    public function createQuoteTransfer(): QuoteTransfer
    {
        $quoteTransfer = (new QuoteBuilder())
            ->withItem()
            ->withItem()
            ->withCustomer()
            ->withTotals()
            ->withShippingAddress()
            ->withBillingAddress()
            ->build();

        $quoteTransfer->setCustomer($this->haveCustomer());

        return $quoteTransfer->setPayment($this->createPaymentTransfer());
    }

    public function createSaveOrderTransfer(): SaveOrderTransfer
    {
        $saveOrderTransfer = (new SaveOrderBuilder())
            ->build();

        return $saveOrderTransfer->setOrderReference(static::ORDER_REFERENCE);
    }

    /**
     * @return \Generated\Shared\Transfer\PaymentTransfer
     */
    public function createPaymentTransfer(): PaymentTransfer
    {
        return (new PaymentTransfer())
            ->setPaymentProvider(static::PAYMENT_PROVIDER)
            ->setPaymentMethod(static::PAYMENT_METHOD)
            ->setPaymentSelection(static::PAYMENT_METHOD)
            ->setUnzerPayment($this->createUnzerPaymentTransfer());
    }


    public function createUnzerPaymentTransfer(): UnzerPaymentTransfer
    {
        return (new UnzerPaymentTransfer())
            ->setCustomer($this->createUnzerCustomer())
            ->setUnzerKeyPair($this->createUnzerKeyPair())
            ->setIsMarketplace(true)
            ->setIsAuthorizable(true);
    }

    public function createUnzerCustomer()
    {
        return (new UnzerCustomerTransfer())
            ->setId(static::UNZER_API_RESPONSE_CUSTOMER_ID);
    }

    /**
     * @return UnzerApiResponseTransfer
     */
    public function createUnzerApiResponseTransfer(): UnzerApiResponseTransfer
    {
        return (new UnzerApiResponseTransfer())
            ->setIsSuccessful(true)
            ->setCreateCustomerResponse($this->createUnzerApiCreateCustomerResponseTransfer())
            ->setUpdateCustomerResponse($this->createUnzerApiUpdateCustomerResponseTransfer())
            ->setCreateMetadataResponse($this->createUnzerApiCreateMetadataResponseTransfer())
            ->setCreateBasketResponse($this->createUnzerApiCreateBasketResponseTransfer())
            ->setCreatePaymentResourceResponse($this->createUnzerApiCreatePaymentResourceResponseTransfer())
            ->setSetWebhookResponse($this->createUnzerApiSetWebhookResponseTransfer());
    }

    /**
     * @return UnzerApiCreateCustomerResponseTransfer
     */
    protected function createUnzerApiCreateCustomerResponseTransfer(): UnzerApiCreateCustomerResponseTransfer
    {
        return (new UnzerApiCreateCustomerResponseTransfer())
            ->setId(static::UNZER_API_RESPONSE_CUSTOMER_ID);
    }

    /**
     * @return UnzerApiUpdateCustomerResponseTransfer
     */
    protected function createUnzerApiUpdateCustomerResponseTransfer(): UnzerApiUpdateCustomerResponseTransfer
    {
        return (new UnzerApiUpdateCustomerResponseTransfer())
            ->setId(static::UNZER_API_RESPONSE_CUSTOMER_ID);
    }

    /**
     * @return UnzerApiCreateMetadataResponseTransfer
     */
    protected function createUnzerApiCreateMetadataResponseTransfer(): UnzerApiCreateMetadataResponseTransfer
    {
        return (new UnzerApiCreateMetadataResponseTransfer())
            ->setId(static::UNZER_API_RESPONSE_METADATA_ID);
    }

    /**
     * @return UnzerApiCreateBasketResponseTransfer
     */
    protected function createUnzerApiCreateBasketResponseTransfer(): UnzerApiCreateBasketResponseTransfer
    {
        return (new UnzerApiCreateBasketResponseTransfer())
            ->setId(static::UNZER_API_RESPONSE_BASKET_ID);
    }

    /**
     * @return UnzerApiCreatePaymentResourceResponseTransfer
     */
    protected function createUnzerApiCreatePaymentResourceResponseTransfer(): UnzerApiCreatePaymentResourceResponseTransfer
    {
        return (new UnzerApiCreatePaymentResourceResponseTransfer())
            ->setId(static::UNZER_API_RESPONSE_PAYMENT_RESOURCE_ID);
    }

    /**
     * @return UnzerApiSetWebhookResponseTransfer
     */
    protected function createUnzerApiSetWebhookResponseTransfer(): UnzerApiSetWebhookResponseTransfer
    {
        return (new UnzerApiSetWebhookResponseTransfer())
            ->setUrl(static::UNZER_API_RESPONSE_WEBHOOK_URL);
    }

    /**
     * @return UnzerNotificationConfigTransfer
     */
    public function createUnzerNotificationConfigTransfer(): UnzerNotificationConfigTransfer
    {
        return (new UnzerNotificationConfigTransfer())
            ->setUnzerKeyPair($this->createUnzerKeyPair())
            ->setUrl(static::UNZER_API_RESPONSE_WEBHOOK_URL)
        ;
    }

    /**
     * @return UnzerKeypairTransfer
     */
    public function createUnzerKeyPair(): UnzerKeypairTransfer
    {
        return (new UnzerKeypairTransfer())
            ->setKeypairId(static::UNZER_KEYPAIR_ID)
            ->setPrivateKey(static::UNZER_PRIVATE_KEY)
            ->setPublicKey(static::UNZER_PUBLIC_KEY);
    }

    public function createUnzerNotificationTransfer()
    {
        return (new UnzerNotificationTransfer())
            ->setEvent(static::UNZER_EVENT_AUTHORIZED)
            ->setPublicKey(static::UNZER_PUBLIC_KEY)
            ->setPaymentId(static::UNZER_PAYMENT_ID);
    }
}
