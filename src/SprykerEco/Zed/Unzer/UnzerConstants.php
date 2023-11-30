<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer;

interface UnzerConstants
{
    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_ALIPAY = 'alipay';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_APPLE_PAY = 'applepay';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_BANCONTACT = 'bancontact';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_BANK_TRANSFER = 'pis';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_CREDIT_CARD = 'card';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_DIRECT_DEBIT = 'sepa-direct-debit';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_DIRECT_DEBIT_SECURED = 'sepa-direct-debit-secured';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_ELECTRONIC_PAYMENT_STANDARD = 'eps';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_GIROPAY = 'giropay';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_IDEAL = 'ideal';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_INVOICE = 'invoice';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_INVOICE_SECURED = 'invoice-secured';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_PAYPAL = 'paypal';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_POST_FINANCE_CARD = 'post-finance-card';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_POST_FINANCE_EFINANCE = 'post-finance-efinance';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_PREPAYMENT = 'prepayment';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_PRZELEWY24 = 'przelewy24';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_SOFORT = 'sofort';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_WECHAT_PAY = 'wechatpay';

    /**
     * @var string
     */
    public const UNZER_PAYMENT_METHOD_INSTALLMENT_SECURED = 'installment-secured';

    /**
     * @var int
     */
    public const UNZER_PAYMENT_STATUS_PENDING = 0;

    /**
     * @var int
     */
    public const UNZER_PAYMENT_STATUS_COMPLETED = 1;

    /**
     * @var int
     */
    public const UNZER_PAYMENT_STATUS_CANCELED = 2;

    /**
     * @var int
     */
    public const UNZER_PAYMENT_STATUS_CHARGE_BACK = 5;

    /**
     * @var string
     */
    public const OMS_STATUS_NEW = 'new';

    /**
     * @var string
     */
    public const OMS_STATUS_AUTHORIZE_PENDING = 'authorize pending';

    /**
     * @var string
     */
    public const OMS_STATUS_AUTHORIZE_SUCCEEDED = 'authorize succeeded';

    /**
     * @var string
     */
    public const OMS_STATUS_AUTHORIZE_FAILED = 'authorize failed';

    /**
     * @var string
     */
    public const OMS_STATUS_AUTHORIZE_CANCELED = 'authorize canceled';

    /**
     * @var string
     */
    public const OMS_STATUS_CHARGE_PENDING = 'charge pending';

    /**
     * @var string
     */
    public const OMS_STATUS_CHARGE_FAILED = 'charge failed';

    /**
     * @var string
     */
    public const OMS_STATUS_CHARGE_REFUNDED = 'refunded';

    /**
     * @var string
     */
    public const OMS_STATUS_PAYMENT_PENDING = 'payment pending';

    /**
     * @var string
     */
    public const OMS_STATUS_PAYMENT_COMPLETED = 'payment completed';

    /**
     * @var string
     */
    public const OMS_STATUS_PAYMENT_CANCELLED = 'payment canceled';

    /**
     * @var string
     */
    public const OMS_STATUS_PAYMENT_CHARGEBACK = 'payment chargeback';

    /**
     * @var string
     */
    public const NOTIFICATION_TYPE_AUTHORIZE_SUCCESS = 'authorize.succeeded';

    /**
     * @var string
     */
    public const NOTIFICATION_TYPE_AUTHORIZE_PENDING = 'authorize.pending';

    /**
     * @var string
     */
    public const NOTIFICATION_TYPE_AUTHORIZE_FAILED = 'authorize.failed';

    /**
     * @var string
     */
    public const NOTIFICATION_TYPE_AUTHORIZE_CANCELED = 'authorize.canceled';

    /**
     * @var string
     */
    public const NOTIFICATION_TYPE_CHARGE_PENDING = 'charge.pending';

    /**
     * @var string
     */
    public const NOTIFICATION_TYPE_CHARGE_FAILED = 'charge.failed';

    /**
     * @var string
     */
    public const NOTIFICATION_TYPE_CHARGE_SUCCEEDED = 'charge.succeeded';

    /**
     * @var string
     */
    public const NOTIFICATION_TYPE_PAYMENT_COMPLETED = 'payment.completed';

    /**
     * @var string
     */
    public const NOTIFICATION_TYPE_PAYMENT_CANCELED = 'payment.canceled';

    /**
     * @var string
     */
    public const NOTIFICATION_TYPE_PAYMENT_CHARGEBACK = 'payment.chargeback';

    /**
     * @var int
     */
    public const PARTIAL_REFUND_QUANTITY = 0;

    /**
     * @var int
     */
    public const FULL_REFUND_QUANTITY = 1;

    /**
     * @var string
     */
    public const TRANSACTION_TYPE_CHARGE = 'charge';

    /**
     * @var string
     */
    public const TRANSACTION_TYPE_AUTHORIZE = 'authorize';

    /**
     * @var string
     */
    public const TRANSACTION_STATUS_SUCCESS = 'success';

    /**
     * @var string
     */
    public const TRANSACTION_STATUS_PENDING = 'pending';

    /**
     * @var string
     */
    public const TRANSACTION_STATUS_ERROR = 'error';

    /**
     * @var int
     */
    public const INT_TO_FLOAT_DIVIDER = 100;

    /**
     * @var string
     */
    public const WEBHOOK_EVENT_TYPE = 'all';

    /**
     * @var string
     */
    public const UNZER_BASKET_TYPE_GOODS = 'goods';

    /**
     * @var string
     */
    public const UNZER_BASKET_TYPE_SHIPMENTS = 'shipments';

    /**
     * @var string
     */
    public const UNZER_BASKET_SHIPMENT_TITLE = 'Shipping costs';

    /**
     * @var string
     */
    public const UNZER_BASKET_SHIPMENT_REFERENCE_ID = 'shipment_costs';

    /**
     * @var string
     */
    public const UNZER_BASKET_SHIPMENT_REFERENCE_ID_TEMPLATE = self::UNZER_BASKET_SHIPMENT_REFERENCE_ID . '_%s';

    /**
     * @var string
     */
    public const UNZER_BASKET_ITEM_REFERENCE_ID_TEMPLATE = '%s_%s';
}
