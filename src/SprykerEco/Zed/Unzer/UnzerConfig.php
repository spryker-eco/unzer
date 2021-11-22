<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer;

use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\Unzer\UnzerConfig as UnzerSharedConfig;
use SprykerEco\Shared\Unzer\UnzerConstants as UnzerSharedConstants;

class UnzerConfig extends AbstractBundleConfig
{
    /**
     * @var array<string, string>
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
     * @var array<string, string>
     */
    protected const UNZER_PAYMENT_METHODS_MAP = [
        UnzerSharedConfig::PAYMENT_METHOD_MARKETPLACE_CREDIT_CARD => UnzerConstants::UNZER_PAYMENT_METHOD_CARD,
        UnzerSharedConfig::PAYMENT_METHOD_MARKETPLACE_BANK_TRANSFER => UnzerConstants::UNZER_PAYMENT_METHOD_BANK_TRANSFER,
        UnzerSharedConfig::PAYMENT_METHOD_MARKETPLACE_SOFORT => UnzerConstants::UNZER_PAYMENT_METHOD_SOFORT,
    ];

    /**
     * @var array<string>
     */
    protected const AUTHORIZE_PAYMENT_METHODS = [
        UnzerSharedConfig::PAYMENT_METHOD_MARKETPLACE_CREDIT_CARD,
    ];

    /**
     * @var array<string>
     */
    protected const MARKETPLACE_READY_PAYMENT_METHODS = [
        UnzerSharedConfig::PAYMENT_METHOD_MARKETPLACE_CREDIT_CARD,
        UnzerSharedConfig::PAYMENT_METHOD_MARKETPLACE_BANK_TRANSFER,
        UnzerSharedConfig::PAYMENT_METHOD_MARKETPLACE_SOFORT,
    ];

    /**
     * @var array<string>
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
     * @param string $paymentMethodName
     *
     * @return string
     */
    public function getUnzerPaymentMethodKey(string $paymentMethodName): string
    {
        return static::UNZER_PAYMENT_METHODS_MAP[$paymentMethodName];
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
    public function getProviderName(): string
    {
        return UnzerSharedConfig::PAYMENT_PROVIDER_NAME;
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
    public function getUnzerPrimaryKeypairId()
    {
        return $this->get(UnzerSharedConstants::PRIMARY_KEYPAIR_ID);
    }
}
