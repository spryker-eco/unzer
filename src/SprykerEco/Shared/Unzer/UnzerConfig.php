<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Shared\Unzer;

use Spryker\Shared\Kernel\AbstractBundleConfig;

class UnzerConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const PAYMENT_PROVIDER_NAME = 'unzer';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_MARKETPLACE_SOFORT = self::PAYMENT_PROVIDER_NAME . 'MarketplaceSofort';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_MARKETPLACE_CREDIT_CARD = self::PAYMENT_PROVIDER_NAME . 'MarketplaceCreditCard';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_MARKETPLACE_BANK_TRANSFER = self::PAYMENT_PROVIDER_NAME . 'MarketplaceBankTransfer';
}
