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
     * @api
     *
     * @var string
     */
    public const PAYMENT_PROVIDER_NAME = 'Unzer';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_PROVIDER_TYPE = 'unzer';

    /**
     * @api
     *
     * @var string
     */
    public const PLATFORM_MARKETPLACE = 'Marketplace';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_ALIPAY = self::PAYMENT_PROVIDER_NAME . ' Alipay';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_APPLE_PAY = self::PAYMENT_PROVIDER_NAME . ' Apple Pay';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_BANCONTACT = self::PAYMENT_PROVIDER_NAME . ' Bancontact';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_BANK_TRANSFER = self::PAYMENT_PROVIDER_NAME . ' Bank Transfer';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_CREDIT_CARD = self::PAYMENT_PROVIDER_NAME . ' Credit Card';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_DIRECT_DEBIT = self::PAYMENT_PROVIDER_NAME . ' Direct Debit';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_DIRECT_DEBIT_SECURED = self::PAYMENT_PROVIDER_NAME . ' Direct Debit Secured';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_ELECTRONIC_PAYMENT_STANDARD = self::PAYMENT_PROVIDER_NAME . ' Electronic Payment Standard (EPS)';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_GIROPAY = self::PAYMENT_PROVIDER_NAME . ' Giropay';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_IDEAL = self::PAYMENT_PROVIDER_NAME . ' iDeal';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_INVOICE = self::PAYMENT_PROVIDER_NAME . ' Invoice';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_INVOICE_SECURED = self::PAYMENT_PROVIDER_NAME . ' Invoice Secured';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_PAYPAL = self::PAYMENT_PROVIDER_NAME . ' PayPal';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_POST_FINANCE_CARD = self::PAYMENT_PROVIDER_NAME . ' PostFinance Card';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_POST_FINANCE_EFINANCE = self::PAYMENT_PROVIDER_NAME . ' PostFinance e-finance';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_PREPAYMENT = self::PAYMENT_PROVIDER_NAME . ' Prepayment';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_PRZELEWY24 = self::PAYMENT_PROVIDER_NAME . ' Przelewy24';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_SOFORT = self::PAYMENT_PROVIDER_NAME . ' Sofort';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_WECHAT_PAY = self::PAYMENT_PROVIDER_NAME . ' WeChat Pay';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_INSTALLMENT_SECURE = self::PAYMENT_PROVIDER_NAME . ' Installment Secured';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_MARKETPLACE_BANK_TRANSFER = self::PAYMENT_PROVIDER_NAME . ' ' . self::PLATFORM_MARKETPLACE . ' Bank Transfer';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_MARKETPLACE_CREDIT_CARD = self::PAYMENT_PROVIDER_NAME . ' ' . self::PLATFORM_MARKETPLACE . ' Credit Card';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_NAME_MARKETPLACE_SOFORT = self::PAYMENT_PROVIDER_NAME . ' ' . self::PLATFORM_MARKETPLACE . ' Sofort';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_ALIPAY = self::PAYMENT_PROVIDER_TYPE . 'Alipay';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_APPLE_PAY = self::PAYMENT_PROVIDER_TYPE . 'ApplePay';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_BANCONTACT = self::PAYMENT_PROVIDER_TYPE . 'Bancontact';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_BANK_TRANSFER = self::PAYMENT_PROVIDER_TYPE . 'BankTransfer';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_CREDIT_CARD = self::PAYMENT_PROVIDER_TYPE . 'CreditCard';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_DIRECT_DEBIT = self::PAYMENT_PROVIDER_TYPE . 'DirectDebit';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_DIRECT_DEBIT_SECURED = self::PAYMENT_PROVIDER_TYPE . 'DirectDebitSecured';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_ELECTRONIC_PAYMENT_STANDARD = self::PAYMENT_PROVIDER_TYPE . 'ElectronicPaymentStandard';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_GIROPAY = self::PAYMENT_PROVIDER_TYPE . 'Giropay';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_IDEAL = self::PAYMENT_PROVIDER_TYPE . 'IDeal';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_INVOICE = self::PAYMENT_PROVIDER_TYPE . 'Invoice';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_INVOICE_SECURED = self::PAYMENT_PROVIDER_TYPE . 'InvoiceSecured';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_PAYPAL = self::PAYMENT_PROVIDER_TYPE . 'PayPal';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_POST_FINANCE_CARD = self::PAYMENT_PROVIDER_TYPE . 'PostFinanceCard';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_POST_FINANCE_EFINANCE = self::PAYMENT_PROVIDER_TYPE . 'PostFinanceEFinance';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_PREPAYMENT = self::PAYMENT_PROVIDER_TYPE . 'Prepayment';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_PRZELEWY24 = self::PAYMENT_PROVIDER_TYPE . 'Przelewy24';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_SOFORT = self::PAYMENT_PROVIDER_TYPE . 'Sofort';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_WECHAT_PAY = self::PAYMENT_PROVIDER_TYPE . 'WeChatPay';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_INSTALLMENT_SECURED = self::PAYMENT_PROVIDER_TYPE . 'InstallmentSecured';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_MARKETPLACE_BANK_TRANSFER = self::PAYMENT_PROVIDER_TYPE . self::PLATFORM_MARKETPLACE . 'BankTransfer';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_MARKETPLACE_CREDIT_CARD = self::PAYMENT_PROVIDER_TYPE . self::PLATFORM_MARKETPLACE . 'CreditCard';

    /**
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_KEY_MARKETPLACE_SOFORT = self::PAYMENT_PROVIDER_TYPE . self::PLATFORM_MARKETPLACE . 'Sofort';
}
