<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer;

use Codeception\Actor;
use Codeception\Scenario;
use Generated\Shared\DataBuilder\QuoteBuilder;
use Generated\Shared\DataBuilder\SaveOrderBuilder;
use Generated\Shared\DataBuilder\UnzerConfigBuilder;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\UnzerApiCreateBasketResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreateCustomerResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreateMetadataResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreatePaymentResourceResponseTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use Generated\Shared\Transfer\UnzerApiSetWebhookResponseTransfer;
use Generated\Shared\Transfer\UnzerApiUpdateCustomerResponseTransfer;
use Generated\Shared\Transfer\UnzerConfigTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerNotificationConfigTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
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
     * @var string
     */
    public const PAYMENT_SELECTION_MARKETPLACE = 'unzerMarketplaceBankTransfer';

    /**
     * @var int
     */
    public const TOTALS_PRICE_TO_PAY = 72350;

    /**
     * @var string
     */
    public const ADDRESS_COUNTRY = 'Germany';

    /**
     * @var string
     */
    public const ADDRESS_CITY = 'Berlin';

    /**
     * @var string
     */
    public const ADDRESS_ZIP = '20537';

    /**
     * @var string
     */
    public const ADDRESS_ADDITIONAL = 'street';

    /**
     * @var int
     */
    public const ADDRESS_NO = 324324;

    /**
     * @var string
     */
    public const ADDRESS_STREET = 'Street';

    /**
     * @var string
     */
    public const CUSTOMER_SALUTATION = 'Mr';

    /**
     * @var string
     */
    public const CUSTOMER_SURNAME = 'Doe';

    /**
     * @var string
     */
    public const CUSTOMER_NAME = 'John';

    /**
     * @var string
     */
    public const CUSTOMER_EMAIL = 'john.doe@mail.com';

    /**
     * @var string
     */
    public const CUSTOMER_REFERENCE = 'DE-22';

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
    public const ORDER_REFERENCE = 'ORD-DE-12';

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
            ->setSetWebhookResponse($this->createUnzerApiSetWebhookResponseTransfer());
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
     * @return \Generated\Shared\Transfer\UnzerNotificationConfigTransfer
     */
    public function createUnzerNotificationConfigTransfer(): UnzerNotificationConfigTransfer
    {
        return (new UnzerNotificationConfigTransfer())
            ->setUnzerKeyPair($this->createUnzerKeyPair())
            ->setUrl(static::UNZER_API_RESPONSE_WEBHOOK_URL);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    public function createUnzerKeyPair(): UnzerKeypairTransfer
    {
        return (new UnzerKeypairTransfer())
            ->setKeypairId(static::UNZER_KEYPAIR_ID)
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
     * @return \Generated\Shared\Transfer\UnzerConfigTransfer
     */
    public function createUnzerConfigTransfer(): UnzerConfigTransfer
    {
        return (new UnzerConfigBuilder())->build()
            ->setUnzerKeypair($this->createUnzerKeyPair());
    }
}
