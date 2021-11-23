<?php

namespace SprykerEcoTest\Zed\Unzer;

use Codeception\Actor;
use Codeception\Scenario;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerAddressTransfer;
use Generated\Shared\Transfer\UnzerApiAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiChargeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreateBasketRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreateCustomerRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreateMetadataRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreatePaymentResourceRequestTransfer;
use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiMarketplaceRefundRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRefundRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiSetWebhookRequestTransfer;
use Generated\Shared\Transfer\UnzerApiUpdateCustomerRequestTransfer;
use Generated\Shared\Transfer\UnzerApiUpdateCustomerResponseTransfer;
use Generated\Shared\Transfer\UnzerBasketItemTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as SharedUnzerConfig;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Shared\UnzerApi\UnzerApiConstants;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerBasketAdapter;
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
    const TOTALS_PRICE_TO_PAY = ;
    const ADDRESS_COUNTRY = ;
    const ADDRESS_CITY = ;
    const ADDRESS_ZIP = ;
    const ADDRESS_ADDITIONAL = ;
    const ADDRESS_NO = ;
    const ADDRESS_STREET = ;
    const CUSTOMER_SALUTATION = ;
    const CUSTOMER_SURNAME = ;
    const CUSTOMER_NAME = ;
    const CUSTOMER_SALUTATION = ;
    const CUSTOMER_SURNAME = ;
    const CUSTOMER_NAME = ;
    const CUSTOMER_EMAIL = ;
    const CUSTOMER_REFERENCE = ;
    const PAYMENT_METHOD = ;
    const PAYMENT_METHOD = ;
    const PAYMENT_PROVIDER = ;

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
        return (new QuoteTransfer())
            ->setPayment($this->createPaymentTransfer())
            ->setCustomer($this->createCustomerTransfer())
            ->setShippingAddress($this->createAddressTransfer())
            ->setBillingAddress($this->createAddressTransfer())
            ->setTotals()
            ->addItem()
            ->addItem()
    }

    /**
     * @return \Generated\Shared\Transfer\PaymentTransfer
     */
    public function createPaymentTransfer(): PaymentTransfer
    {
        return (new PaymentTransfer())
            ->setPaymentProvider(static::PAYMENT_PROVIDER)
            ->setPaymentMethod(static::PAYMENT_METHOD)
            ->setPaymentSelection(static::PAYMENT_METHOD);
    }

    public function createUnzerPaymentTransfer(): UnzerPaymentTransfer
    {
        return (new UnzerPaymentTransfer());
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function createCustomerTransfer(): CustomerTransfer
    {
        return (new CustomerTransfer())
            ->setIsGuest(false)
            ->setCustomerReference(static::CUSTOMER_REFERENCE)
            ->setEmail(static::CUSTOMER_EMAIL)
            ->setFirstName(static::CUSTOMER_NAME)
            ->setLastName(static::CUSTOMER_SURNAME)
            ->setSalutation(static::CUSTOMER_SALUTATION);
    }

    /**
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function createAddressTransfer(): AddressTransfer
    {
        return (new AddressTransfer())
            ->setLastName(static::CUSTOMER_NAME)
            ->setLastName(static::CUSTOMER_SURNAME)
            ->setSalutation(static::CUSTOMER_SALUTATION)
            ->setAddress1(static::ADDRESS_STREET)
            ->setAddress2(static::ADDRESS_NO)
            ->setAddress3(static::ADDRESS_ADDITIONAL)
            ->setZipCode(static::ADDRESS_ZIP)
            ->setCity(static::ADDRESS_CITY)
            ->setIso2Code(static::ADDRESS_COUNTRY);
    }

    /**
     * @return \Generated\Shared\Transfer\TotalsTransfer
     */
    public function createTotalsTransfer(): TotalsTransfer
    {
        return (new TotalsTransfer())
            ->setPriceToPay(static::TOTALS_PRICE_TO_PAY)
            ->setTaxTotal($this->createTaxTotalTransfer());
    }




}
