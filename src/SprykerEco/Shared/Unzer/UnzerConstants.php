<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Shared\Unzer;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface UnzerConstants
{
    /**
     * Specification:
     * - Unzer authorize return URL.
     *
     * @api
     *
     * @var string
     */
    public const UNZER_AUTHORIZE_RETURN_URL = 'UNZER:UNZER_AUTHORIZE_RETURN_URL';

    /**
     * Specification:
     * - Unzer charge return URL.
     *
     * @api
     *
     * @var string
     */
    public const UNZER_CHARGE_RETURN_URL = 'UNZER:UNZER_CHARGE_RETURN_URL';

    /**
     * Specification:
     * - Unzer webhook retrieve URL.
     *
     * @api
     *
     * @var string
     */
    public const WEBHOOK_RETRIEVE_URL = 'UNZER:WEBHOOK_RETRIEVE_URL';

    /**
     * Specification:
     * - .
     *
     * @api
     *
     * @var string
     */
    public const MASTER_MERCHANT_PARTICIPANT_ID = 'UNZER:MASTER_MERCHANT_PARTICIPANT_ID';

    /**
     * Specification:
     * - .
     *
     * @api
     *
     * @var string
     */
    public const VAULT_DATA_TYPE = 'UNZER:VAULT_DATA_TYPE';

    /**
     * Specification:
     * - Unzer expenses refund strategy key.
     *
     * @api
     *
     * @var string
     */
    public const EXPENSES_REFUND_STRATEGY_KEY = 'UNZER:EXPENSES_REFUND_STRATEGY_KEY';

    /**
     * Specification:
     * - Unzer Credentials type `Standard`.
     *
     * @api
     *
     * @var int
     */
    public const UNZER_CREDENTIALS_TYPE_STANDARD = 1;

    /**
     * Specification:
     * - Unzer Credentials type `Main Marketplace`.
     *
     * @api
     *
     * @var int
     */
    public const UNZER_CREDENTIALS_TYPE_MAIN_MARKETPLACE = 2;

    /**
     * Specification:
     * - Unzer Credentials type `Marketplace Main Merchant`.
     *
     * @api
     *
     * @var int
     */
    public const UNZER_CREDENTIALS_TYPE_MARKETPLACE_MAIN_MERCHANT = 3;

    /**
     * Specification:
     * - Unzer Credentials type `Marketplace Merchant`.
     *
     * @api
     *
     * @var int
     */
    public const UNZER_CREDENTIALS_TYPE_MARKETPLACE_MERCHANT = 4;

    /**
     * Specification:
     * - Refund strategy `No expenses`.
     *
     * @api
     *
     * @var int
     */
    public const NO_EXPENSES_REFUND_STRATEGY = 0;

    /**
     * Specification:
     * - Refund strategy `Last shipment item refund`.
     *
     * @api
     *
     * @var int
     */
    public const LAST_SHIPMENT_ITEM_EXPENSES_REFUND_STRATEGY = 1;

    /**
     * Specification:
     * - Refund strategy `Last order item refund`.
     *
     * @api
     *
     * @var int
     */
    public const LAST_ORDER_ITEM_EXPENSES_REFUND_STRATEGY = 2;

    /**
     * Specification:
     * - Main seller reference.
     *
     * @api
     *
     * @var string
     */
    public const MAIN_SELLER_REFERENCE = 'main';

    /**
     * Specification:
     * - Unzer Credentials main types.
     *
     * @api
     *
     * @var array<int>
     */
    public const UNZER_CREDENTIALS_MAIN_TYPES = [
        self::UNZER_CREDENTIALS_TYPE_STANDARD,
        self::UNZER_CREDENTIALS_TYPE_MAIN_MARKETPLACE,
    ];

    /**
     * Specification:
     * - Unzer Credentials child types.
     *
     * @api
     *
     * @var array<int>
     */
    public const UNZER_CREDENTIALS_CHILD_TYPES = [
        self::UNZER_CREDENTIALS_TYPE_MARKETPLACE_MAIN_MERCHANT,
        self::UNZER_CREDENTIALS_TYPE_MARKETPLACE_MERCHANT,
    ];
}
