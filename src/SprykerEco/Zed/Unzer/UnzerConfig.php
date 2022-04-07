<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer;

use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\Unzer\UnzerConfig as UnzerSharedConfig;
use SprykerEco\Shared\Unzer\UnzerConstants as UnzerSharedConstants;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;

class UnzerConfig extends AbstractBundleConfig
{
    /**
     * @var array<int, string>
     */
    protected const UNZER_PAYMENT_STATE_OMS_STATUS_MAP = [
        UnzerConstants::UNZER_PAYMENT_STATUS_PENDING => UnzerConstants::OMS_STATUS_PAYMENT_PENDING,
        UnzerConstants::UNZER_PAYMENT_STATUS_COMPLETED => UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED,
        UnzerConstants::UNZER_PAYMENT_STATUS_CANCELED => UnzerConstants::OMS_STATUS_PAYMENT_CANCELLED,
        UnzerConstants::UNZER_PAYMENT_STATUS_CHARGE_BACK => UnzerConstants::OMS_STATUS_PAYMENT_CHARGEBACK,
    ];

    /**
     * @var array<string, string>
     */
    protected const UNZER_EVENT_OMS_STATUS_MAP = [
        UnzerConstants::NOTIFICATION_TYPE_AUTHORIZE_SUCCESS => UnzerConstants::OMS_STATUS_AUTHORIZE_SUCCEEDED,
        UnzerConstants::NOTIFICATION_TYPE_AUTHORIZE_PENDING => UnzerConstants::OMS_STATUS_AUTHORIZE_PENDING,
        UnzerConstants::NOTIFICATION_TYPE_AUTHORIZE_FAILED => UnzerConstants::OMS_STATUS_AUTHORIZE_FAILED,
        UnzerConstants::NOTIFICATION_TYPE_AUTHORIZE_CANCELED => UnzerConstants::OMS_STATUS_AUTHORIZE_CANCELED,
        UnzerConstants::NOTIFICATION_TYPE_CHARGE_PENDING => UnzerConstants::OMS_STATUS_CHARGE_PENDING,
        UnzerConstants::NOTIFICATION_TYPE_CHARGE_FAILED => UnzerConstants::OMS_STATUS_CHARGE_FAILED,
        UnzerConstants::NOTIFICATION_TYPE_PAYMENT_COMPLETED => UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED,
    ];

    /**
     * @var array<string, array<int, string>>
     */
    protected const UNZER_PAYMENT_METHOD_KEYS_MAP = [
        UnzerConstants::UNZER_PAYMENT_METHOD_ALIPAY => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_ALIPAY,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_APPLE_PAY => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_APPLE_PAY,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_BANCONTACT => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_BANCONTACT,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_BANK_TRANSFER => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_BANK_TRANSFER,
            UnzerSharedConfig::PAYMENT_METHOD_KEY_MARKETPLACE_BANK_TRANSFER,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_CREDIT_CARD => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_CREDIT_CARD,
            UnzerSharedConfig::PAYMENT_METHOD_KEY_MARKETPLACE_CREDIT_CARD,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_DIRECT_DEBIT => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_DIRECT_DEBIT,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_DIRECT_DEBIT_SECURED => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_DIRECT_DEBIT_SECURED,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_ELECTRONIC_PAYMENT_STANDARD => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_ELECTRONIC_PAYMENT_STANDARD,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_GIROPAY => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_GIROPAY,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_IDEAL => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_IDEAL,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_INVOICE => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_INVOICE,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_INVOICE_SECURED => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_INVOICE_SECURED,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_PAYPAL => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_PAYPAL,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_POST_FINANCE_CARD => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_POST_FINANCE_CARD,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_POST_FINANCE_EFINANCE => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_POST_FINANCE_EFINANCE,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_PREPAYMENT => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_PREPAYMENT,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_PRZELEWY24 => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_PRZELEWY24,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_SOFORT => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_SOFORT,
            UnzerSharedConfig::PAYMENT_METHOD_KEY_MARKETPLACE_SOFORT,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_WECHAT_PAY => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_WECHAT_PAY,
        ],
        UnzerConstants::UNZER_PAYMENT_METHOD_INSTALLMENT_SECURED => [
            UnzerSharedConfig::PAYMENT_METHOD_KEY_INSTALLMENT_SECURED,
        ],
    ];

    /**
     * @var array<int, string>
     */
    protected const AUTHORIZE_PAYMENT_METHODS = [
        UnzerSharedConfig::PAYMENT_METHOD_KEY_MARKETPLACE_CREDIT_CARD,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_CREDIT_CARD,
    ];

    /**
     * @var array<string, string>
     */
    protected const UNZER_PAYMENT_METHOD_NAMES_MAP = [
        UnzerSharedConfig::PAYMENT_METHOD_KEY_ALIPAY => UnzerSharedConfig::PAYMENT_METHOD_NAME_ALIPAY,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_APPLE_PAY => UnzerSharedConfig::PAYMENT_METHOD_NAME_APPLE_PAY,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_BANCONTACT => UnzerSharedConfig::PAYMENT_METHOD_NAME_BANCONTACT,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_BANK_TRANSFER => UnzerSharedConfig::PAYMENT_METHOD_NAME_BANK_TRANSFER,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_CREDIT_CARD => UnzerSharedConfig::PAYMENT_METHOD_NAME_CREDIT_CARD,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_DIRECT_DEBIT => UnzerSharedConfig::PAYMENT_METHOD_NAME_DIRECT_DEBIT,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_DIRECT_DEBIT_SECURED => UnzerSharedConfig::PAYMENT_METHOD_NAME_DIRECT_DEBIT_SECURED,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_ELECTRONIC_PAYMENT_STANDARD => UnzerSharedConfig::PAYMENT_METHOD_NAME_ELECTRONIC_PAYMENT_STANDARD,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_GIROPAY => UnzerSharedConfig::PAYMENT_METHOD_NAME_GIROPAY,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_IDEAL => UnzerSharedConfig::PAYMENT_METHOD_NAME_IDEAL,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_INVOICE => UnzerSharedConfig::PAYMENT_METHOD_NAME_INVOICE,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_INVOICE_SECURED => UnzerSharedConfig::PAYMENT_METHOD_NAME_INVOICE_SECURED,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_PAYPAL => UnzerSharedConfig::PAYMENT_METHOD_NAME_PAYPAL,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_POST_FINANCE_CARD => UnzerSharedConfig::PAYMENT_METHOD_NAME_POST_FINANCE_CARD,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_POST_FINANCE_EFINANCE => UnzerSharedConfig::PAYMENT_METHOD_NAME_POST_FINANCE_EFINANCE,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_PREPAYMENT => UnzerSharedConfig::PAYMENT_METHOD_NAME_PREPAYMENT,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_PRZELEWY24 => UnzerSharedConfig::PAYMENT_METHOD_NAME_PRZELEWY24,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_SOFORT => UnzerSharedConfig::PAYMENT_METHOD_NAME_SOFORT,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_WECHAT_PAY => UnzerSharedConfig::PAYMENT_METHOD_NAME_WECHAT_PAY,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_MARKETPLACE_BANK_TRANSFER => UnzerSharedConfig::PAYMENT_METHOD_NAME_MARKETPLACE_BANK_TRANSFER,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_MARKETPLACE_CREDIT_CARD => UnzerSharedConfig::PAYMENT_METHOD_NAME_MARKETPLACE_CREDIT_CARD,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_MARKETPLACE_SOFORT => UnzerSharedConfig::PAYMENT_METHOD_NAME_MARKETPLACE_SOFORT,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_INSTALLMENT_SECURED => UnzerSharedConfig::PAYMENT_METHOD_NAME_INSTALLMENT_SECURE,
    ];

    /**
     * @var array<int, string>
     */
    protected const MARKETPLACE_READY_PAYMENT_METHODS = [
        UnzerSharedConfig::PAYMENT_METHOD_KEY_MARKETPLACE_CREDIT_CARD,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_MARKETPLACE_BANK_TRANSFER,
        UnzerSharedConfig::PAYMENT_METHOD_KEY_MARKETPLACE_SOFORT,
    ];

    /**
     * @var array<int, string>
     */
    protected const ENABLED_UNZER_NOTIFICATIONS = [
        UnzerConstants::NOTIFICATION_TYPE_AUTHORIZE_SUCCESS,
        UnzerConstants::NOTIFICATION_TYPE_AUTHORIZE_FAILED,
        UnzerConstants::NOTIFICATION_TYPE_AUTHORIZE_CANCELED,

        UnzerConstants::NOTIFICATION_TYPE_CHARGE_PENDING,
        UnzerConstants::NOTIFICATION_TYPE_CHARGE_FAILED,

        UnzerConstants::NOTIFICATION_TYPE_PAYMENT_COMPLETED,
        UnzerConstants::NOTIFICATION_TYPE_PAYMENT_CANCELED,
        UnzerConstants::NOTIFICATION_TYPE_PAYMENT_CHARGEBACK,
    ];

    /**
     * @var array<string, string>
     */
    public const SALUTATION_MAP = [
        'Mr' => 'mr',
        'Mrs' => 'mrs',
        'Ms' => 'mrs',
        'Dr' => 'mr',
    ];

    /**
     * @var string
     */
    public const SALUTATION_DEFAULT = 'unknown';

    /**
     * @api
     *
     * @param string $paymentMethodName
     *
     * @return bool
     */
    public function isPaymentMethodMarketplaceReady(string $paymentMethodName): bool
    {
        return in_array($paymentMethodName, static::MARKETPLACE_READY_PAYMENT_METHODS, true);
    }

    /**
     * @api
     *
     * @param string $paymentMethodName
     *
     * @return bool
     */
    public function isPaymentAuthorizeRequired(string $paymentMethodName): bool
    {
        return in_array($paymentMethodName, static::AUTHORIZE_PAYMENT_METHODS, true);
    }

    /**
     * @api
     *
     * @param string $paymentMethodKey
     *
     * @return string
     */
    public function getUnzerPaymentMethodKey(string $paymentMethodKey): string
    {
        foreach (static::UNZER_PAYMENT_METHOD_KEYS_MAP as $unzerPaymentMethodKey => $paymentMethodKeys) {
            if (in_array($paymentMethodKey, $paymentMethodKeys)) {
                return $unzerPaymentMethodKey;
            }
        }

        throw new UnzerException('Unknown Unzer payment method key ' . $paymentMethodKey . ' detected.');
    }

    /**
     * @api
     *
     * @param string $paymentMethodKey
     *
     * @return string
     */
    public function getPaymentMethodName(string $paymentMethodKey): string
    {
        return static::UNZER_PAYMENT_METHOD_NAMES_MAP[$paymentMethodKey];
    }

    /**
     * @api
     *
     * @param string $unzerPaymentMethodKey
     *
     * @return array<array-key, string>
     */
    public function getPaymentMethodKeys(string $unzerPaymentMethodKey): array
    {
        if (!isset(static::UNZER_PAYMENT_METHOD_KEYS_MAP[$unzerPaymentMethodKey])) {
            return [];
        }

        return static::UNZER_PAYMENT_METHOD_KEYS_MAP[$unzerPaymentMethodKey];
    }

    /**
     * @api
     *
     * @return string
     */
    public function getAuthorizeReturnUrl(): string
    {
        return (string)$this->get(UnzerSharedConstants::UNZER_AUTHORIZE_RETURN_URL);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getChargeReturnUrl(): string
    {
        return (string)$this->get(UnzerSharedConstants::UNZER_CHARGE_RETURN_URL);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getWebhookRetrieveUrl(): string
    {
        return (string)$this->get(UnzerSharedConstants::WEBHOOK_RETRIEVE_URL);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getOmsStatusNew(): string
    {
        return UnzerConstants::OMS_STATUS_NEW;
    }

    /**
     * @api
     *
     * @param int $unzerStateId
     *
     * @return string
     */
    public function mapUnzerPaymentStatusToOmsStatus(int $unzerStateId): string
    {
        return static::UNZER_PAYMENT_STATE_OMS_STATUS_MAP[$unzerStateId];
    }

    /**
     * @api
     *
     * @param string $eventType
     *
     * @return bool
     */
    public function isNotificationTypeEnabled(string $eventType): bool
    {
        return in_array($eventType, static::ENABLED_UNZER_NOTIFICATIONS, true);
    }

    /**
     * @api
     *
     * @param string $unzerEvent
     *
     * @return string
     */
    public function mapUnzerEventToOmsStatus(string $unzerEvent): string
    {
        return static::UNZER_EVENT_OMS_STATUS_MAP[$unzerEvent];
    }

    /**
     * @api
     *
     * @return string
     */
    public function getOmsStatusAuthorizeSucceeded(): string
    {
        return UnzerConstants::OMS_STATUS_AUTHORIZE_SUCCEEDED;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getOmsStatusAuthorizePending(): string
    {
        return UnzerConstants::OMS_STATUS_AUTHORIZE_PENDING;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getOmsStatusAuthorizeFailed(): string
    {
        return UnzerConstants::OMS_STATUS_AUTHORIZE_FAILED;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getOmsStatusAuthorizeCanceled(): string
    {
        return UnzerConstants::OMS_STATUS_AUTHORIZE_CANCELED;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getOmsStatusPaymentCompleted(): string
    {
        return UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getOmsStatusChargeFailed(): string
    {
        return UnzerConstants::OMS_STATUS_CHARGE_FAILED;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getOmsStatusChargeback(): string
    {
        return UnzerConstants::OMS_STATUS_PAYMENT_CHARGEBACK;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getPaymentProviderName(): string
    {
        return UnzerSharedConfig::PAYMENT_PROVIDER_NAME;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getPaymentProviderType(): string
    {
        return UnzerSharedConfig::PAYMENT_PROVIDER_TYPE;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getMasterMerchantParticipantId(): string
    {
        return $this->get(UnzerSharedConstants::MASTER_MERCHANT_PARTICIPANT_ID);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getUnzerPrimaryKeypairId(): string
    {
        return $this->get(UnzerSharedConstants::MAIN_REGULAR_KEYPAIR_ID);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getVaultDataType(): string
    {
        return $this->get(UnzerSharedConstants::VAULT_DATA_TYPE);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getWebhookEventType(): string
    {
        return UnzerConstants::WEBHOOK_EVENT_TYPE;
    }

    /**
     * @api
     *
     * @return int
     */
    public function getExpensesRefundStrategyKey(): int
    {
        return $this->get(UnzerSharedConstants::EXPENSES_REFUND_STRATEGY_KEY);
    }
}
