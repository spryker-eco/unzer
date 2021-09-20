<?php

namespace SprykerEco\Zed\Unzer;

interface UnzerConstants
{
    public const UNZER_PAYMENT_METHOD_CARD = 'card';
    public const UNZER_PAYMENT_METHOD_BANK_TRANSFER = 'pis';
    public const UNZER_PAYMENT_METHOD_SOFORT = 'sofort';
    public const UNZER_PAYMENT_METHOD_PAYPAL = 'paypal';
    public const UNZER_PAYMENT_METHOD_INVOICE_SECURED = 'invoice-secured';
    public const UNZER_PAYMENT_METHOD_INSTALMENT = 'installment-secured';

    public const UNZER_PAYMENT_STATUS_PENDING = 0;
    public const UNZER_PAYMENT_STATUS_COMPLETED = 1;
    public const UNZER_PAYMENT_STATUS_CANCELED = 2;
    public const UNZER_PAYMENT_STATUS_PARTLY = 3;
    public const UNZER_PAYMENT_STATUS_REVIEW = 4;
    public const UNZER_PAYMENT_STATUS_CHARGE_BACK = 5;

    public const OMS_STATUS_NEW = 'new';
    public const OMS_STATUS_AUTHORIZE_PENDING = 'authorize pending';
    public const OMS_STATUS_AUTHORIZE_SUCCEEDED = 'authorize succeeded';
    public const OMS_STATUS_AUTHORIZE_FAILED = 'authorize failed';
    public const OMS_STATUS_AUTHORIZE_CANCELED = 'authorize canceled';
    public const OMS_STATUS_CHARGE_PENDING = 'charge pending';
    public const OMS_STATUS_CHARGE_FAILED = 'charge failed';
    public const OMS_STATUS_CHARGE_REFUNDED = 'refunded';
    public const OMS_STATUS_PAYMENT_PENDING = 'payment pending';
    public const OMS_STATUS_PAYMENT_COMPLETED = 'payment completed';
    public const OMS_STATUS_PAYMENT_CANCELLED = 'payment canceled';
    public const OMS_STATUS_PAYMENT_CHARGEBACK = 'payment chargeback';

    public const NOTIFICATION_TYPE_AUTHORIZE_SUCCESS = 'authorize.succeeded';
    public const NOTIFICATION_TYPE_AUTHORIZE_PENDING = 'authorize.pending';
    public const NOTIFICATION_TYPE_AUTHORIZE_FAILED = 'authorize.failed';
    public const NOTIFICATION_TYPE_AUTHORIZE_CANCELED = 'authorize.canceled';
    public const NOTIFICATION_TYPE_CHARGE_PENDING = 'charge.pending';
    public const NOTIFICATION_TYPE_CHARGE_FAILED = 'charge.failed';
    public const NOTIFICATION_TYPE_PAYMENT_COMPLETED = 'payment.completed';
    public const NOTIFICATION_TYPE_PAYMENT_CANCELED = 'payment.canceled';
    public const NOTIFICATION_TYPE_PAYMENT_CHARGEBACK = 'payment.chargeback';

    public const PARTIAL_REFUND_QUANTITY = 0;
    public const TRANSACTION_TYPE_CHARGE = 'charge';
}
