<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Shared\Unzer;

use Spryker\Shared\Kernel\AbstractSharedConfig;

class UnzerConfig extends AbstractSharedConfig
{
    /**
     * Specification:
     * - Unzer payment provider name.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_PROVIDER_NAME = 'Unzer';

    /**
     * Specification:
     * - Unzer payment provider type.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_PROVIDER_TYPE = 'unzer';

    /**
     * Specification:
     * - Marketplace platform.
     *
     * @api
     *
     * @var string
     */
    public const PLATFORM_MARKETPLACE = 'Marketplace';

    /**
     * Specification:
     * - Payment method `AliPay`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_ALIPAY = self::PAYMENT_PROVIDER_NAME . ' Alipay';

    /**
     * Specification:
     * - Payment method `Apple Pay`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_APPLE_PAY = self::PAYMENT_PROVIDER_NAME . ' Apple Pay';

    /**
     * Specification:
     * - Payment method `Unzer Bancontact`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_BANCONTACT = self::PAYMENT_PROVIDER_NAME . ' Bancontact';

    /**
     * Specification:
     * - Payment method `Unzer Bank Transfer`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_BANK_TRANSFER = self::PAYMENT_PROVIDER_NAME . ' Bank Transfer';

    /**
     * Specification:
     * - Payment method `Unzer Credit Card`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_CREDIT_CARD = self::PAYMENT_PROVIDER_NAME . ' Credit Card';

    /**
     * Specification:
     * - Payment method `Unzer Direct Debit`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_DIRECT_DEBIT = self::PAYMENT_PROVIDER_NAME . ' Direct Debit';

    /**
     * Specification:
     * - Payment method `Unzer Direct Debit Secured`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_DIRECT_DEBIT_SECURED = self::PAYMENT_PROVIDER_NAME . ' Direct Debit Secured';

    /**
     * Specification:
     * - Payment method `Unzer Electronic Payment Standard (EPS)`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_ELECTRONIC_PAYMENT_STANDARD = self::PAYMENT_PROVIDER_NAME . ' Electronic Payment Standard (EPS)';

    /**
     * Specification:
     * - Payment method `Unzer Giropay`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_GIROPAY = self::PAYMENT_PROVIDER_NAME . ' Giropay';

    /**
     * Specification:
     * - Payment method `Unzer iDeal`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_IDEAL = self::PAYMENT_PROVIDER_NAME . ' iDeal';

    /**
     * Specification:
     * - Payment method `Unzer Invoice`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_INVOICE = self::PAYMENT_PROVIDER_NAME . ' Invoice';

    /**
     * Specification:
     * - Payment method `Unzer Invoice Secured`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_INVOICE_SECURED = self::PAYMENT_PROVIDER_NAME . ' Invoice Secured';

    /**
     * Specification:
     * - Payment method `Unzer PayPal`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_PAYPAL = self::PAYMENT_PROVIDER_NAME . ' PayPal';

    /**
     * Specification:
     * - Payment method `Unzer PostFinance Card`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_POST_FINANCE_CARD = self::PAYMENT_PROVIDER_NAME . ' PostFinance Card';

    /**
     * Specification:
     * - Payment method `Unzer PostFinance e-finance`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_POST_FINANCE_EFINANCE = self::PAYMENT_PROVIDER_NAME . ' PostFinance e-finance';

    /**
     * Specification:
     * - Payment method `Unzer Prepayment`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_PREPAYMENT = self::PAYMENT_PROVIDER_NAME . ' Prepayment';

    /**
     * Specification:
     * - Payment method `Unzer Przelewy24`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_PRZELEWY24 = self::PAYMENT_PROVIDER_NAME . ' Przelewy24';

    /**
     * Specification:
     * - Payment method `Unzer Sofort`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_SOFORT = self::PAYMENT_PROVIDER_NAME . ' Sofort';

    /**
     * Specification:
     * - Payment method `Unzer WeChat Pay`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_WECHAT_PAY = self::PAYMENT_PROVIDER_NAME . ' WeChat Pay';

    /**
     * Specification:
     * - Payment method `Unzer Installment Secured`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_INSTALLMENT_SECURE = self::PAYMENT_PROVIDER_NAME . ' Installment Secured';

    /**
     * Specification:
     * - Payment method `Unzer Marketplace Bank Transfer`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_MARKETPLACE_BANK_TRANSFER = self::PAYMENT_PROVIDER_NAME . ' ' . self::PLATFORM_MARKETPLACE . ' Bank Transfer';

    /**
     * Specification:
     * - Payment method `Unzer Marketplace Credit Card`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_MARKETPLACE_CREDIT_CARD = self::PAYMENT_PROVIDER_NAME . ' ' . self::PLATFORM_MARKETPLACE . ' Credit Card';

    /**
     * Specification:
     * - Payment method `Unzer Marketplace Sofort`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_MARKETPLACE_SOFORT = self::PAYMENT_PROVIDER_NAME . ' ' . self::PLATFORM_MARKETPLACE . ' Sofort';

    /**
     * Specification:
     * - Payment method key `unzerAlipay`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_ALIPAY = self::PAYMENT_PROVIDER_TYPE . 'Alipay';

    /**
     * Specification:
     * - Payment method key `unzerApplePay`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_APPLE_PAY = self::PAYMENT_PROVIDER_TYPE . 'ApplePay';

    /**
     * Specification:
     * - Payment method key `unzerBancontact`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_BANCONTACT = self::PAYMENT_PROVIDER_TYPE . 'Bancontact';

    /**
     * Specification:
     * - Payment method key `unzerBankTransfer`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_BANK_TRANSFER = self::PAYMENT_PROVIDER_TYPE . 'BankTransfer';

    /**
     * Specification:
     * - Payment method key `unzerCreditCard`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_CREDIT_CARD = self::PAYMENT_PROVIDER_TYPE . 'CreditCard';

    /**
     * Specification:
     * - Payment method key `unzerDirectDebit`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_DIRECT_DEBIT = self::PAYMENT_PROVIDER_TYPE . 'DirectDebit';

    /**
     * Specification:
     * - Payment method key `unzerDirectDebitSecured`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_DIRECT_DEBIT_SECURED = self::PAYMENT_PROVIDER_TYPE . 'DirectDebitSecured';

    /**
     * Specification:
     * - Payment method key `unzerElectronicPaymentStandard`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_ELECTRONIC_PAYMENT_STANDARD = self::PAYMENT_PROVIDER_TYPE . 'ElectronicPaymentStandard';

    /**
     * Specification:
     * - Payment method key `unzerGiropay`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_GIROPAY = self::PAYMENT_PROVIDER_TYPE . 'Giropay';

    /**
     * Specification:
     * - Payment method key `unzerIDeal`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_IDEAL = self::PAYMENT_PROVIDER_TYPE . 'IDeal';

    /**
     * Specification:
     * - Payment method key `unzerInvoice`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_INVOICE = self::PAYMENT_PROVIDER_TYPE . 'Invoice';

    /**
     * Specification:
     * - Payment method key `unzerInvoiceSecured`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_INVOICE_SECURED = self::PAYMENT_PROVIDER_TYPE . 'InvoiceSecured';

    /**
     * Specification:
     * - Payment method key `unzerPayPal`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_PAYPAL = self::PAYMENT_PROVIDER_TYPE . 'PayPal';

    /**
     * Specification:
     * - Payment method key `unzerPostFinanceCard`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_POST_FINANCE_CARD = self::PAYMENT_PROVIDER_TYPE . 'PostFinanceCard';

    /**
     * Specification:
     * - Payment method key `unzerPostFinanceEFinance`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_POST_FINANCE_EFINANCE = self::PAYMENT_PROVIDER_TYPE . 'PostFinanceEFinance';

    /**
     * Specification:
     * - Payment method key `unzerPrepayment`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_PREPAYMENT = self::PAYMENT_PROVIDER_TYPE . 'Prepayment';

    /**
     * Specification:
     * - Payment method key `unzerPrzelewy24`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_PRZELEWY24 = self::PAYMENT_PROVIDER_TYPE . 'Przelewy24';

    /**
     * Specification:
     * - Payment method key `unzerSofort`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_SOFORT = self::PAYMENT_PROVIDER_TYPE . 'Sofort';

    /**
     * Specification:
     * - Payment method key `unzerWeChatPay`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_WECHAT_PAY = self::PAYMENT_PROVIDER_TYPE . 'WeChatPay';

    /**
     * Specification:
     * - Payment method key `unzerInstallmentSecured`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_INSTALLMENT_SECURED = self::PAYMENT_PROVIDER_TYPE . 'InstallmentSecured';

    /**
     * Specification:
     * - Payment method key `unzerMarketplaceBankTransfer`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_MARKETPLACE_BANK_TRANSFER = self::PAYMENT_PROVIDER_TYPE . self::PLATFORM_MARKETPLACE . 'BankTransfer';

    /**
     * Specification:
     * - Payment method key `unzerMarketplaceCreditCard`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_MARKETPLACE_CREDIT_CARD = self::PAYMENT_PROVIDER_TYPE . self::PLATFORM_MARKETPLACE . 'CreditCard';

    /**
     * Specification:
     * - Payment method key `unzerMarketplaceSofort`.
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_MARKETPLACE_SOFORT = self::PAYMENT_PROVIDER_TYPE . self::PLATFORM_MARKETPLACE . 'Sofort';
}
