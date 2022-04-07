<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer;

use Codeception\Actor;
use Codeception\Scenario;
use Generated\Shared\DataBuilder\CheckoutResponseBuilder;
use Generated\Shared\DataBuilder\PaymentMethodsBuilder;
use Generated\Shared\DataBuilder\QuoteBuilder;
use Generated\Shared\DataBuilder\SaveOrderBuilder;
use Generated\Shared\DataBuilder\StoreRelationBuilder;
use Generated\Shared\DataBuilder\TaxTotalBuilder;
use Generated\Shared\DataBuilder\TotalsBuilder;
use Generated\Shared\DataBuilder\UnzerCredentialsBuilder;
use Generated\Shared\DataBuilder\UnzerKeypairBuilder;
use Generated\Shared\DataBuilder\UnzerNotificationBuilder;
use Generated\Shared\DataBuilder\UnzerPaymentBuilder;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Generated\Shared\Transfer\UnzerApiAuthorizeResponseTransfer;
use Generated\Shared\Transfer\UnzerApiChargeResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreateBasketResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreateCustomerResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreateMetadataResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreatePaymentResourceResponseTransfer;
use Generated\Shared\Transfer\UnzerApiGetPaymentMethodsResponseTransfer;
use Generated\Shared\Transfer\UnzerApiGetPaymentResponseTransfer;
use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeResponseTransfer;
use Generated\Shared\Transfer\UnzerApiPaymentMethodTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use Generated\Shared\Transfer\UnzerApiSetWebhookResponseTransfer;
use Generated\Shared\Transfer\UnzerApiUpdateCustomerResponseTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Orm\Zed\Payment\Persistence\SpyPaymentMethodQuery;
use Orm\Zed\Payment\Persistence\SpyPaymentProviderQuery;
use Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsQuery;
use Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsStoreQuery;
use Spryker\Shared\Vault\VaultConstants;
use SprykerEco\Shared\Unzer\UnzerConfig as UnzerSharedConfig;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerEntityManager;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepository;
use SprykerEco\Zed\Unzer\UnzerConfig;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class UnzerZedTester extends Actor
{
    use _generated\UnzerZedTesterActions;

    /**
     * @var string
     */
    public const UNZER_PRIVATE_KEY = 's-priv';

    /**
     * @var string
     */
    public const ORDER_REFERENCE = 'DE--1';

    /**
     * @var string
     */
    public const UNZER_REDIRECT_URL = 'https://spryker.com';

    /**
     * @var string
     */
    public const MERCHANT_REFERENCE = 'merchant1';

    /**
     * @var array<string>
     */
    public const UNZER_MARKETPLACE_PAYMENT_METHODS = [
        UnzerSharedConfig::PAYMENT_METHOD_KEY_MARKETPLACE_BANK_TRANSFER,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_MARKETPLACE_CREDIT_CARD,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_MARKETPLACE_SOFORT,
    ];

    /**
     * @var int
     */
    protected const TOTALS_PRICE_TO_PAY = 72350;

    /**
     * @uses \SprykerEco\Shared\Unzer\UnzerConfig::PAYMENT_PROVIDER_NAME
     *
     * @var string
     */
    protected const PAYMENT_PROVIDER = 'Unzer';

    /**
     * @var string
     */
    protected const UNZER_API_RESPONSE_CUSTOMER_ID = 's-cust-43434234';

    /**
     * @var string
     */
    protected const UNZER_API_RESPONSE_METADATA_ID = 's-meta-5423423';

    /**
     * @var string
     */
    protected const UNZER_API_RESPONSE_BASKET_ID = 's-bskt-435345';

    /**
     * @var string
     */
    protected const UNZER_API_RESPONSE_PAYMENT_RESOURCE_ID = 's-type-sofort-1';

    /**
     * @var string
     */
    protected const UNZER_API_RESPONSE_WEBHOOK_URL = 'https://unzer-spryker.com';

    /**
     * @var string
     */
    protected const UNZER_MAIN_REGULAR_KEYPAIR_ID = 'keypair-id-1';

    /**
     * @var string
     */
    protected const UNZER_MARKETPLACE_MAIN_MERCHANT_KEYPAIR_ID = 'keypair-id-3';

    /**
     * @var string
     */
    protected const UNZER_MARKETPLACE_MERCHANT_KEYPAIR_ID = 'keypair-id-4';

    /**
     * @var string
     */
    protected const UNZER_PUBLIC_KEY = 's-pub';

    /**
     * @var string
     */
    protected const UNZER_PAYMENT_ID = 's-pay-1234';

    /**
     * @var string
     */
    protected const UNZER_EVENT_AUTHORIZED = 'authorize.succeeded';

    /**
     * @var string
     */
    protected const STATE_MACHINE_PROCESS_NAME = 'UnzerMarketplaceBankTransfer01';

    /**
     * @var int
     */
    protected const UNZER_ORDER_STATE_ID = 1;

    /**
     * @var string
     */
    protected const PAYMENT_METHOD = 'unzerMarketplaceBankTransfer';

    /**
     * @var string
     */
    protected const CURRENCY_CODE = 'EUR';

    /**
     * @var \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface
     */
    protected $facade;

    /**
     * @param \Codeception\Scenario $scenario
     */
    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);

        $this->setUpConfig();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\UnzerConfig
     */
    public function createConfig(): UnzerConfig
    {
        return new UnzerConfig();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManager
     */
    public function createEntityManager(): UnzerEntityManager
    {
        return new UnzerEntityManager();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Persistence\UnzerRepository
     */
    public function createRepository(): UnzerRepository
    {
        return new UnzerRepository();
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function createQuoteTransfer(): QuoteTransfer
    {
        return (new QuoteBuilder())
            ->withItem()
            ->withTotals([
                TotalsTransfer::TAX_TOTAL => (new TaxTotalBuilder())->build(),
            ])
            ->withShippingAddress()
            ->withBillingAddress()
            ->withStore()
            ->withCurrency()
            ->build()
            ->setCustomer($this->haveCustomer())
            ->setStore($this->haveStore());
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function createMarketplaceQuoteTransfer(): QuoteTransfer
    {
        $quoteTransfer = (new QuoteBuilder())
            ->withItem([
                'merchantReference' => static::MERCHANT_REFERENCE,
            ])
            ->withItem([
                'merchantReference' => null,
            ])
            ->withTotals(
                (new TotalsBuilder())->withTaxTotal(),
            )
            ->withCurrency([CurrencyTransfer::CODE => static::CURRENCY_CODE])
            ->withShippingAddress()
            ->withBillingAddress()
            ->build();

        return $quoteTransfer->setCustomer($this->haveCustomer())
            ->setPayment($this->createPaymentTransfer('unzerBankTransfer')->setUnzerPayment($this->createUnzerPaymentTransfer(true, false)))
            ->setStore($this->haveStore());
    }

    /**
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function createCheckoutResponseTransfer(): CheckoutResponseTransfer
    {
        return (new CheckoutResponseBuilder())->build()
            ->setSaveOrder($this->createSaveOrderTransfer());
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiResponseTransfer
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
            ->setSetWebhookResponse($this->createUnzerApiSetWebhookResponseTransfer())
            ->setGetPaymentMethodsResponse($this->createUnzerApiGetPaymentMethodsResponseTransfer())
            ->setGetPaymentResponse($this->createUnzerApiGetPaymentResponseTransfer())
            ->setMarketplaceAuthorizeResponse($this->createUnzerApiMarketplaceAuthorizeResponseTransfer())
            ->setChargeResponse($this->createUnzerApiChargeResponseTransfer())
            ->setAuthorizeResponse($this->createUnzerApiAuthorizeResponseTransfer());
    }

    /**
     * @param array $override
     *
     * @return \Generated\Shared\Transfer\UnzerNotificationTransfer
     */
    public function createUnzerNotificationTransfer(array $override = []): UnzerNotificationTransfer
    {
        return (new UnzerNotificationBuilder(array_merge([
            UnzerNotificationTransfer::PAYMENT_ID => static::UNZER_PAYMENT_ID,
            UnzerNotificationTransfer::EVENT => static::UNZER_EVENT_AUTHORIZED,
        ], $override)))->build();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function haveUnzerEntities(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void
    {
        $this->getFacade()->saveOrderPayment($quoteTransfer, $saveOrderTransfer);
    }

    /**
     * @param \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface $facade
     *
     * @return void
     */
    public function setFacade(UnzerFacadeInterface $facade): void
    {
        $this->facade = $facade;
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface
     */
    public function getFacade(): UnzerFacadeInterface
    {
        return $this->facade;
    }

    /**
     * @return \Generated\Shared\Transfer\SaveOrderTransfer
     */
    public function createOrder(): SaveOrderTransfer
    {
        return $this->haveOrder(
            [
                'unitPrice' => static::TOTALS_PRICE_TO_PAY,
                'sumPrice' => static::TOTALS_PRICE_TO_PAY,
                'orderReference' => static::ORDER_REFERENCE,
            ],
            static::STATE_MACHINE_PROCESS_NAME,
        );
    }

    /**
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function createPaymentMethodsTransfer(): PaymentMethodsTransfer
    {
        $paymentMethodsBuilder = (new PaymentMethodsBuilder())->withMethod();
        foreach (static::UNZER_MARKETPLACE_PAYMENT_METHODS as $paymentMethod) {
            $paymentMethodsBuilder->withAnotherMethod([
                'paymentProvider' => UnzerSharedConfig::PAYMENT_PROVIDER_NAME,
                'paymentMethodKey' => $paymentMethod,
            ]);
        }

        return $paymentMethodsBuilder->build();
    }

    /**
     * @return void
     */
    public function ensureUnzerCredentialsTableIsEmpty(): void
    {
        SpyUnzerCredentialsStoreQuery::create()->deleteAll();
        SpyUnzerCredentialsQuery::create()->deleteAll();
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
        $this->setConfig(UnzerConstants::MAIN_REGULAR_KEYPAIR_ID, 'id');
        $this->setConfig(UnzerConstants::VAULT_DATA_TYPE, 'unzer-private-key');
        $this->setConfig(VaultConstants::ENCRYPTION_KEY, 'key');
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    protected function createUnzerKeyPair(): UnzerKeypairTransfer
    {
        return (new UnzerKeypairTransfer())
            ->setKeypairId(static::UNZER_MAIN_REGULAR_KEYPAIR_ID)
            ->setPrivateKey(static::UNZER_PRIVATE_KEY)
            ->setPublicKey(static::UNZER_PUBLIC_KEY);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    protected function createUnzerCustomer(): UnzerCustomerTransfer
    {
        return (new UnzerCustomerTransfer())
            ->setId(static::UNZER_API_RESPONSE_CUSTOMER_ID);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    protected function createUnzerBasket(): UnzerBasketTransfer
    {
        return (new UnzerBasketTransfer())
            ->setId(static::UNZER_API_RESPONSE_BASKET_ID);
    }

    /**
     * @param bool $isMarketplace
     * @param bool $isAuthorizable
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function createUnzerPaymentTransfer(bool $isMarketplace, bool $isAuthorizable): UnzerPaymentTransfer
    {
        return (new UnzerPaymentBuilder([
            UnzerPaymentTransfer::IS_MARKETPLACE => $isMarketplace,
            UnzerPaymentTransfer::IS_AUTHORIZABLE => $isAuthorizable,
            UnzerPaymentTransfer::CUSTOMER => $this->createUnzerCustomer(),
            UnzerPaymentTransfer::UNZER_KEYPAIR => $this->createUnzerKeyPair(),
            UnzerPaymentTransfer::BASKET => $this->createUnzerBasket(),
            UnzerPaymentTransfer::ORDER_ID => static::ORDER_REFERENCE,
        ]))->build();
    }

    /**
     * @param string $paymentMethod
     *
     * @return \Generated\Shared\Transfer\PaymentTransfer
     */
    public function createPaymentTransfer(string $paymentMethod): PaymentTransfer
    {
        return (new PaymentTransfer())
            ->setPaymentProvider(static::PAYMENT_PROVIDER)
            ->setPaymentMethod($paymentMethod)
            ->setPaymentSelection($paymentMethod);
    }

    /**
     * @param array $override
     *
     * @return \Generated\Shared\Transfer\SaveOrderTransfer
     */
    protected function createSaveOrderTransfer(array $override = []): SaveOrderTransfer
    {
        return (new SaveOrderBuilder([
            SaveOrderTransfer::ORDER_REFERENCE => static::ORDER_REFERENCE,
        ])
        )->build();
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiCreateCustomerResponseTransfer
     */
    protected function createUnzerApiCreateCustomerResponseTransfer(): UnzerApiCreateCustomerResponseTransfer
    {
        return (new UnzerApiCreateCustomerResponseTransfer())
            ->setId(static::UNZER_API_RESPONSE_CUSTOMER_ID);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiUpdateCustomerResponseTransfer
     */
    protected function createUnzerApiUpdateCustomerResponseTransfer(): UnzerApiUpdateCustomerResponseTransfer
    {
        return (new UnzerApiUpdateCustomerResponseTransfer())
            ->setId(static::UNZER_API_RESPONSE_CUSTOMER_ID);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiCreateMetadataResponseTransfer
     */
    protected function createUnzerApiCreateMetadataResponseTransfer(): UnzerApiCreateMetadataResponseTransfer
    {
        return (new UnzerApiCreateMetadataResponseTransfer())
            ->setId(static::UNZER_API_RESPONSE_METADATA_ID);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiCreateBasketResponseTransfer
     */
    protected function createUnzerApiCreateBasketResponseTransfer(): UnzerApiCreateBasketResponseTransfer
    {
        return (new UnzerApiCreateBasketResponseTransfer())
            ->setId(static::UNZER_API_RESPONSE_BASKET_ID);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiCreatePaymentResourceResponseTransfer
     */
    protected function createUnzerApiCreatePaymentResourceResponseTransfer(): UnzerApiCreatePaymentResourceResponseTransfer
    {
        return (new UnzerApiCreatePaymentResourceResponseTransfer())
            ->setId(static::UNZER_API_RESPONSE_PAYMENT_RESOURCE_ID);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiSetWebhookResponseTransfer
     */
    protected function createUnzerApiSetWebhookResponseTransfer(): UnzerApiSetWebhookResponseTransfer
    {
        return (new UnzerApiSetWebhookResponseTransfer())
            ->setUrl(static::UNZER_API_RESPONSE_WEBHOOK_URL);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiGetPaymentMethodsResponseTransfer
     */
    protected function createUnzerApiGetPaymentMethodsResponseTransfer(): UnzerApiGetPaymentMethodsResponseTransfer
    {
        return (new UnzerApiGetPaymentMethodsResponseTransfer())->addPaymentMethod(
            (new UnzerApiPaymentMethodTransfer())->setPaymentMethodKey('sofort'),
        );
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiGetPaymentResponseTransfer
     */
    protected function createUnzerApiGetPaymentResponseTransfer(): UnzerApiGetPaymentResponseTransfer
    {
        return (new UnzerApiGetPaymentResponseTransfer())
            ->setPaymentId(static::UNZER_PAYMENT_ID)
            ->setAmountCharged(static::TOTALS_PRICE_TO_PAY)
            ->setStateId(1)
            ->setOrderId(static::ORDER_REFERENCE);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiChargeResponseTransfer
     */
    protected function createUnzerApiChargeResponseTransfer(): UnzerApiChargeResponseTransfer
    {
        return (new UnzerApiChargeResponseTransfer())
            ->setIsSuccessful(true)
            ->setRedirectUrl(static::UNZER_REDIRECT_URL)
            ->setAmountCharged(static::TOTALS_PRICE_TO_PAY / 100)
            ->setOrderId(static::ORDER_REFERENCE)
            ->setStateId(static::UNZER_ORDER_STATE_ID)
            ->setCustomerId(static::UNZER_API_RESPONSE_CUSTOMER_ID)
            ->setBasketId(static::UNZER_API_RESPONSE_BASKET_ID);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeResponseTransfer
     */
    protected function createUnzerApiMarketplaceAuthorizeResponseTransfer(): UnzerApiMarketplaceAuthorizeResponseTransfer
    {
        return (new UnzerApiMarketplaceAuthorizeResponseTransfer())
            ->setIsSuccessful(true)
            ->setRedirectUrl(static::UNZER_REDIRECT_URL)
            ->setCustomerId(static::UNZER_API_RESPONSE_CUSTOMER_ID)
            ->setBasketId(static::UNZER_API_RESPONSE_BASKET_ID);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiAuthorizeResponseTransfer
     */
    protected function createUnzerApiAuthorizeResponseTransfer(): UnzerApiAuthorizeResponseTransfer
    {
        return (new UnzerApiAuthorizeResponseTransfer())
            ->setIsSuccessful(true)
            ->setRedirectUrl(static::UNZER_REDIRECT_URL)
            ->setCustomerId(static::UNZER_API_RESPONSE_CUSTOMER_ID)
            ->setBasketId(static::UNZER_API_RESPONSE_BASKET_ID);
    }

    /**
     * @param array $override
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function haveUnzerCredentials(array $override = []): UnzerCredentialsTransfer
    {
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder(array_merge([
            UnzerCredentialsTransfer::UNZER_KEYPAIR => (new UnzerKeypairBuilder())->build(),
            UnzerCredentialsTransfer::STORE_RELATION => $this->createStoreRelation(),
        ], $override)))->build();

        return $this->getFacade()
            ->createUnzerCredentials($unzerCredentialsTransfer)
            ->getUnzerCredentials();
    }

    /**
     * @param array $override
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function haveStandardUnzerCredentials(array $override = []): UnzerCredentialsTransfer
    {
        return $this->haveUnzerCredentials(array_merge([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
        ], $override));
    }

    /**
     * @param array $mainMarketplaceOverride
     * @param array $merchantMainMarketplaceOverride
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function haveMarketplaceUnzerCredentials(array $mainMarketplaceOverride = [], array $merchantMainMarketplaceOverride = []): UnzerCredentialsTransfer
    {
        $mainMarketplaceUnzerCredentialsTransfer = $this->haveUnzerCredentials(array_merge([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE,
        ], $mainMarketplaceOverride));

        $marketplaceMainMerchantUnzerCredentialsTransfer = $this->haveUnzerCredentials(array_merge([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT,
            UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => $mainMarketplaceUnzerCredentialsTransfer->getIdUnzerCredentials(),
        ], $merchantMainMarketplaceOverride));

        return $mainMarketplaceUnzerCredentialsTransfer->setChildUnzerCredentials($marketplaceMainMerchantUnzerCredentialsTransfer);
    }

    /**
     * @param array $override
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function haveMarketplaceMerchantUnzerCredentials(array $override = []): UnzerCredentialsTransfer
    {
        return $this->haveUnzerCredentials(array_merge([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT,
        ], $override));
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer|null $storeTransfer
     *
     * @return \Generated\Shared\Transfer\StoreRelationTransfer
     */
    public function createStoreRelation(?StoreTransfer $storeTransfer = null): StoreRelationTransfer
    {
        $storeTransfer = $storeTransfer ?? $this->haveStore([StoreTransfer::NAME => 'DE']);

        return (new StoreRelationBuilder([
            StoreRelationTransfer::ID_STORES => [
                $storeTransfer->getIdStore(),
            ],
        ]))->build()->addStores($storeTransfer);
    }

    /**
     * @return int
     */
    public function getNumberOfPaymentProviders(): int
    {
        return SpyPaymentProviderQuery::create()->count();
    }

    /**
     * @return int
     */
    public function getNumberOfPaymentMethods(): int
    {
        return SpyPaymentMethodQuery::create()->count();
    }
}
