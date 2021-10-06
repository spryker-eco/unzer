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
}
