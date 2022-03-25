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
     * @var string
     */
    public const UNZER_AUTHORIZE_RETURN_URL = 'UNZER:UNZER_AUTHORIZE_RETURN_URL';

    /**
     * @var string
     */
    public const UNZER_CHARGE_RETURN_URL = 'UNZER:UNZER_CHARGE_RETURN_URL';

    /**
     * @var string
     */
    public const WEBHOOK_RETRIEVE_URL = 'UNZER:WEBHOOK_RETRIEVE_URL';

    /**
     * @var string
     */
    public const MASTER_MERCHANT_PARTICIPANT_ID = 'UNZER:MASTER_MERCHANT_PARTICIPANT_ID';

    /**
     * @var string
     */
    public const MAIN_REGULAR_KEYPAIR_ID = 'UNZER:PRIMARY_KEYPAIR_ID';

    /**
     * @var string
     */
    public const MAIN_MARKETPLACE_KEYPAIR_ID = 'UNZER:PRIMARY_KEYPAIR_ID';

    /**
     * @var string
     */
    public const VAULT_DATA_TYPE = 'UNZER:VAULT_DATA_TYPE';

    /**
     * @var int
     */
    public const UNZER_CONFIG_TYPE_STANDARD = 1;

    /**
     * @var int
     */
    public const UNZER_CONFIG_TYPE_MAIN_MARKETPLACE = 2;

    /**
     * @var int
     */
    public const UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT = 3;

    /**
     * @var int
     */
    public const UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT = 4;

    /**
     * @var array<int,string>
     */
    public const UNZER_CONFIG_TYPES = [
        self::UNZER_CONFIG_TYPE_STANDARD => 'Standard',
        self::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE => 'Marketplace (Main channel)',
        self::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT => 'Marketplace (Main merchant)',
        self::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT => 'Marketplace (Sub-merchant)',
    ];

    /**
     * @var array<int>
     */
    public const UNZER_CHILD_CONFIG_TYPES = [
        self::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT,
        self::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT,
    ];
}
