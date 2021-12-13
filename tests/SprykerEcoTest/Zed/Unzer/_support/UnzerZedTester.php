<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer;

use Codeception\Actor;
use Codeception\Scenario;
use Generated\Shared\DataBuilder\CheckoutResponseBuilder;
use Generated\Shared\DataBuilder\PaymentMethodBuilder;
use Generated\Shared\DataBuilder\PaymentMethodsBuilder;
use Generated\Shared\DataBuilder\QuoteBuilder;
use Generated\Shared\DataBuilder\SaveOrderBuilder;
use Generated\Shared\DataBuilder\TotalsBuilder;
use Generated\Shared\DataBuilder\UnzerCredentialsBuilder;
use Generated\Shared\DataBuilder\UnzerPaymentBuilder;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UnzerApiChargeResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreateBasketResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreateCustomerResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreateMetadataResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreatePaymentResourceResponseTransfer;
use Generated\Shared\Transfer\UnzerApiGetPaymentResponseTransfer;
use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeResponseTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use Generated\Shared\Transfer\UnzerApiSetWebhookResponseTransfer;
use Generated\Shared\Transfer\UnzerApiUpdateCustomerResponseTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
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
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class UnzerZedTester extends Actor
{
    use _generated\UnzerZedTesterActions;

    /**
     * @var int
     */
    public const TOTALS_PRICE_TO_PAY = 72350;

    /**
     * @var string
     */
    public const ADDRESS_CITY = 'Berlin';

    /**
     * @var string
     */
    public const PAYMENT_METHOD = 'unzerMarketplaceBankTransfer';

    /**
     * @var string
     */
    public const PAYMENT_PROVIDER = 'unzer';

    /**
     * @var string
     */
    public const UNZER_API_RESPONSE_CUSTOMER_ID = 's-cust-43434234';

    /**
     * @var string
     */
    public const UNZER_API_RESPONSE_METADATA_ID = 's-meta-5423423';

    /**
     * @var string
     */
    public const UNZER_API_RESPONSE_BASKET_ID = 's-bskt-435345';

    /**
     * @var string
     */
    public const UNZER_API_RESPONSE_PAYMENT_RESOURCE_ID = 's-type-sofort-1';

    /**
     * @var string
     */
    public const UNZER_API_RESPONSE_WEBHOOK_URL = 'https://unzer-spryker.com';

    /**
     * @var string
     */
    public const UNZER_KEYPAIR_ID = 'key-1';

    /**
     * @var string
     */
    public const UNZER_MAIN_REGULAR_KEYPAIR_ID = 'keypair-id-1';

    /**
     * @var string
     */
    public const UNZER_MAIN_MARKETPLACE_KEYPAIR_ID = 'keypair-id-2';

    /**
     * @var string
     */
    public const UNZER_MARKETPLACE_MAIN_MERCHANT_KEYPAIR_ID = 'keypair-id-3';

    /**
     * @var string
     */
    public const UNZER_MARKETPLACE_MERCHANT_KEYPAIR_ID = 'keypair-id-4';

    /**
     * @var string
     */
    public const MERCHANT_REFERENCE = 'merchant1';

    /**
     * @var string
     */
    public const UNZER_PUBLIC_KEY = 's-pub';

    /**
     * @var string
     */
    public const UNZER_PRIVATE_KEY = 's-priv';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_ID = 's-pay-1234';

    /**
     * @var string
     */
    public const UNZER_EVENT_AUTHORIZED = 'authorize.succeeded';

    /**
     * @var string
     */
    public const ORDER_REFERENCE = 'DE--19';

    /**
     * @var string
     */
    public const STATE_MACHINE_PROCESS_NAME = 'UnzerMarketplaceBankTransfer01';

    /**
     * @var string
     */
    public const UNZER_REDIRECT_URL = 'https://spryker.com';

    /**
     * @var int
     */
    public const UNZER_ORDER_STATE_ID = 1;

    /**
     * @var array<string>
     */
    public const UNZER_MARKETPLACE_PAYMENT_METHODS = [
        UnzerSharedConfig::PAYMENT_METHOD_MARKETPLACE_CREDIT_CARD,
        UnzerSharedConfig::PAYMENT_METHOD_MARKETPLACE_BANK_TRANSFER,
        UnzerSharedConfig::PAYMENT_METHOD_MARKETPLACE_SOFORT,
    ];

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
        $this->setConfig(UnzerConstants::MAIN_REGULAR_KEYPAIR_ID, 'id');
        $this->setConfig(UnzerConstants::VAULT_DATA_TYPE, 'unzer-private-key');
        $this->setConfig(VaultConstants::ENCRYPTION_KEY, 'key');
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
        $quoteTransfer = (new QuoteBuilder())
            ->withItem()
            ->withTotals()
            ->withShippingAddress()
            ->withBillingAddress()
            ->withStore()
            ->build();

        return $quoteTransfer
            ->setCustomer($this->haveCustomer())
            ->setStore($this->haveStore())
            ->setPayment($this->createPaymentTransfer());
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
                (new TotalsBuilder())
                ->withTaxTotal(),
            )
            ->withCurrency()
            ->withShippingAddress()
            ->withBillingAddress()
            ->build();

        return $quoteTransfer
            ->setCustomer($this->haveCustomer())
            ->setPayment($this->createPaymentTransfer())
            ->setStore($this->haveStore());
    }

    /**
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function createCheckoutResponseTransfer(): CheckoutResponseTransfer
    {
        return (new CheckoutResponseBuilder())
            ->build()
            ->setSaveOrder($this->createSaveOrderTransfer());
    }

    /**
     * @return \Generated\Shared\Transfer\SaveOrderTransfer
     */
    public function createSaveOrderTransfer(): SaveOrderTransfer
    {
        return (new SaveOrderBuilder())
            ->build()
            ->setOrderReference(static::ORDER_REFERENCE);
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
            ->setUnzerPayment($this->createUnzerPaymentTransfer(true, true));
    }

    /**
     * @param bool $isMarketplace
     * @param bool $isAuthorizable
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function createUnzerPaymentTransfer(bool $isMarketplace, bool $isAuthorizable): UnzerPaymentTransfer
    {
        $unzerPaymentTransfer = (new UnzerPaymentBuilder())
            ->build();

        return $unzerPaymentTransfer
            ->setId(static::UNZER_PAYMENT_ID)
            ->setOrderId(static::ORDER_REFERENCE)
            ->setBasket($this->createUnzerBasket())
            ->setCustomer($this->createUnzerCustomer())
            ->setUnzerKeypair($this->createUnzerKeyPair())
            ->setIsMarketplace($isMarketplace)
            ->setIsAuthorizable($isAuthorizable);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function createUnzerCustomer(): UnzerCustomerTransfer
    {
        return (new UnzerCustomerTransfer())
            ->setId(static::UNZER_API_RESPONSE_CUSTOMER_ID);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    public function createUnzerBasket(): UnzerBasketTransfer
    {
        return (new UnzerBasketTransfer())
            ->setId(static::UNZER_API_RESPONSE_BASKET_ID);
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
            ->setGetPaymentResponse($this->createUnzerApiGetPaymentResponseTransfer())
            ->setMarketplaceAuthorizeResponse($this->createUnzerApiMarketplaceAuthorizeResponseTransfer())
            ->setChargeResponse($this->createUnzerApiChargeResponseTransfer());
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    public function createUnzerKeyPair(): UnzerKeypairTransfer
    {
        return (new UnzerKeypairTransfer())
            ->setKeypairId(static::UNZER_MAIN_REGULAR_KEYPAIR_ID)
            ->setPrivateKey(static::UNZER_PRIVATE_KEY)
            ->setPublicKey(static::UNZER_PUBLIC_KEY);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerNotificationTransfer
     */
    public function createUnzerNotificationTransfer(): UnzerNotificationTransfer
    {
        return (new UnzerNotificationTransfer())
            ->setEvent(static::UNZER_EVENT_AUTHORIZED)
            ->setPublicKey(static::UNZER_PUBLIC_KEY)
            ->setPaymentId(static::UNZER_PAYMENT_ID);
    }

    /**
     * @param int $type
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function createUnzerCredentialsTransfer(int $type): UnzerCredentialsTransfer
    {
        return (new UnzerCredentialsBuilder())->build()
            ->setKeypairId(static::UNZER_KEYPAIR_ID)
            ->setUnzerKeypair($this->createUnzerKeyPair())
            ->setType($type);
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param string $keypairId
     * @param string|int $type
     * @param string|null $merchantReference
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function createUnzerCredentialsCustomTransfer(
        StoreTransfer $storeTransfer,
        string $keypairId,
        int $type,
        ?string $merchantReference = null
    ): UnzerCredentialsTransfer {
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            'keypairId' => $keypairId,
            'type' => $type,
            'merchantReference' => $merchantReference,
        ]))
            ->withUnzerKeypair([
                'keypairId' => $keypairId,
            ])
            ->build();

        $storeRelation = (new StoreRelationTransfer())->addStores($storeTransfer)->addIdStores($storeTransfer->getIdStore());

        return $unzerCredentialsTransfer->setStoreRelation($storeRelation);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function haveUnzerEntities(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void
    {
        $this->getUnzerFacade()
            ->saveOrderPayment($quoteTransfer, $saveOrderTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function haveUnzerCredentials(StoreTransfer $storeTransfer): UnzerCredentialsResponseTransfer
    {
        return $this->getUnzerFacade()->createUnzerCredentials(
            $this->createUnzerCredentialsCustomTransfer(
                $storeTransfer,
                static::UNZER_MAIN_REGULAR_KEYPAIR_ID,
                UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
            ),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return void
     */
    public function haveMarketplaceUnzerCredentials(StoreTransfer $storeTransfer): void
    {
        $this->getUnzerFacade()->createUnzerCredentials(
            $this->createUnzerCredentialsCustomTransfer(
                $storeTransfer,
                static::UNZER_MAIN_MARKETPLACE_KEYPAIR_ID,
                UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE,
            ),
        );
        $this->getUnzerFacade()->createUnzerCredentials(
            $this->createUnzerCredentialsCustomTransfer(
                $storeTransfer,
                static::UNZER_MARKETPLACE_MAIN_MERCHANT_KEYPAIR_ID,
                UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT,
            ),
        );
        $this->getUnzerFacade()->createUnzerCredentials(
            $this->createUnzerCredentialsCustomTransfer(
                $storeTransfer,
                static::UNZER_MARKETPLACE_MERCHANT_KEYPAIR_ID,
                UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT,
                static::MERCHANT_REFERENCE,
            ),
        );
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
     * @return \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface
     */
    protected function getUnzerFacade(): UnzerFacadeInterface
    {
        return $this->getLocator()->unzer()->facade();
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

    public function createPaymentMethodsTransfer()
    {
        $paymentMethodsBuilder = (new PaymentMethodsBuilder())->withMethod();
        foreach (static::UNZER_MARKETPLACE_PAYMENT_METHODS as $paymentMethod) {
            $paymentMethodsBuilder->withAnotherMethod(
                (new PaymentMethodBuilder([
                    'paymentProvider' => UnzerSharedConfig::PAYMENT_PROVIDER_NAME,
                    'methodName' => $paymentMethod,
                ])),
            );
        }

        return $paymentMethodsBuilder->build();
    }
}
