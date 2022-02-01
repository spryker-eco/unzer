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
     * @var string
     */
    public const PAYMENT_PROVIDER_NAME = 'Unzer';

    /**
     * @var string
     */
    public const PAYMENT_PROVIDER_TYPE = 'unzer';

    /**
     * @var string
     */
    public const PLATFORM_MARKETPLACE = 'Marketplace';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_ALIPAY = self::PAYMENT_PROVIDER_NAME . ' Alipay';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_APPLE_PAY = self::PAYMENT_PROVIDER_NAME . ' Apple Pay';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_BANCONTACT = self::PAYMENT_PROVIDER_NAME . ' Bancontact';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_BANK_TRANSFER = self::PAYMENT_PROVIDER_NAME . ' Bank Transfer';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_CREDIT_CARD = self::PAYMENT_PROVIDER_NAME . ' Credit Card';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_DIRECT_DEBIT = self::PAYMENT_PROVIDER_NAME . ' Direct Debit';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_DIRECT_DEBIT_SECURED = self::PAYMENT_PROVIDER_NAME . ' Direct Debit Secured';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_ELECTRONIC_PAYMENT_STANDARD = self::PAYMENT_PROVIDER_NAME . ' Electronic Payment Standard (EPS)';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_GIROPAY = self::PAYMENT_PROVIDER_NAME . ' Giropay';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_IDEAL = self::PAYMENT_PROVIDER_NAME . ' iDeal';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_INVOICE = self::PAYMENT_PROVIDER_NAME . ' Invoice';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_INVOICE_SECURED = self::PAYMENT_PROVIDER_NAME . ' Invoice Secured';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_PAYPAL = self::PAYMENT_PROVIDER_NAME . ' PayPal';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_POST_FINANCE_CARD = self::PAYMENT_PROVIDER_NAME . ' PostFinance Card';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_POST_FINANCE_EFINANCE = self::PAYMENT_PROVIDER_NAME . ' PostFinance e-finance';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_PREPAYMENT = self::PAYMENT_PROVIDER_NAME . ' Prepayment';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_PRZELEWY24 = self::PAYMENT_PROVIDER_NAME . ' Przelewy24';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_SOFORT = self::PAYMENT_PROVIDER_NAME . ' Sofort';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_WECHAT_PAY = self::PAYMENT_PROVIDER_NAME . ' WeChat Pay';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_MARKETPLACE_BANK_TRANSFER = self::PAYMENT_PROVIDER_NAME . ' ' . self::PLATFORM_MARKETPLACE . ' Bank Transfer';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_MARKETPLACE_CREDIT_CARD = self::PAYMENT_PROVIDER_NAME . ' ' . self::PLATFORM_MARKETPLACE . ' Credit Card';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_NAME_MARKETPLACE_SOFORT = self::PAYMENT_PROVIDER_NAME . ' ' . self::PLATFORM_MARKETPLACE . ' Sofort';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_ALIPAY = self::PAYMENT_PROVIDER_TYPE . 'Alipay';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_APPLE_PAY = self::PAYMENT_PROVIDER_TYPE . 'ApplePay';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_BANCONTACT = self::PAYMENT_PROVIDER_TYPE . 'Bancontact';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_BANK_TRANSFER = self::PAYMENT_PROVIDER_TYPE . 'BankTransfer';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_CREDIT_CARD = self::PAYMENT_PROVIDER_TYPE . 'CreditCard';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_DIRECT_DEBIT = self::PAYMENT_PROVIDER_TYPE . 'DirectDebit';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_DIRECT_DEBIT_SECURED = self::PAYMENT_PROVIDER_TYPE . 'DirectDebitSecured';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_ELECTRONIC_PAYMENT_STANDARD = self::PAYMENT_PROVIDER_TYPE . 'ElectronicPaymentStandard';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_GIROPAY = self::PAYMENT_PROVIDER_TYPE . 'Giropay';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_IDEAL = self::PAYMENT_PROVIDER_TYPE . 'IDeal';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_INVOICE = self::PAYMENT_PROVIDER_TYPE . 'Invoice';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_INVOICE_SECURED = self::PAYMENT_PROVIDER_TYPE . 'InvoiceSecured';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_PAYPAL = self::PAYMENT_PROVIDER_TYPE . 'PayPal';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_POST_FINANCE_CARD = self::PAYMENT_PROVIDER_TYPE . 'PostFinanceCard';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_POST_FINANCE_EFINANCE = self::PAYMENT_PROVIDER_TYPE . 'PostFinanceEFinance';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_PREPAYMENT = self::PAYMENT_PROVIDER_TYPE . 'Prepayment';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_PRZELEWY24 = self::PAYMENT_PROVIDER_TYPE . 'Przelewy24';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_SOFORT = self::PAYMENT_PROVIDER_TYPE . 'Sofort';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_WECHAT_PAY = self::PAYMENT_PROVIDER_TYPE . 'WeChatPay';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_MARKETPLACE_BANK_TRANSFER = self::PAYMENT_PROVIDER_TYPE . self::PLATFORM_MARKETPLACE . 'BankTransfer';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_MARKETPLACE_CREDIT_CARD = self::PAYMENT_PROVIDER_TYPE . self::PLATFORM_MARKETPLACE . 'CreditCard';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_KEY_MARKETPLACE_SOFORT = self::PAYMENT_PROVIDER_TYPE . self::PLATFORM_MARKETPLACE . 'Sofort';
}
