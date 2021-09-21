<?php declare(strict_types = 1);

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
    public const PROVIDER_NAME = 'unzer';

    public const PAYMENT_METHOD_MARKETPLACE_SOFORT = self::PROVIDER_NAME . 'MarketplaceSofort';
    public const PAYMENT_METHOD_MARKETPLACE_CREDIT_CARD = self::PROVIDER_NAME . 'MarketplaceCreditCard';
    public const PAYMENT_METHOD_MARKETPLACE_BANK_TRANSFER = self::PROVIDER_NAME . 'MarketplaceBankTransfer';

    /**
     * @api
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return static::PROVIDER_NAME;
    }
}
