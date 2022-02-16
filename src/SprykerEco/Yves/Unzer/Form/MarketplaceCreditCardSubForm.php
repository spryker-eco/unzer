<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Form;

use SprykerEco\Shared\Unzer\UnzerConfig;

class MarketplaceCreditCardSubForm extends CreditCardSubForm
{
    /**
     * @var string
     */
    protected const PAYMENT_METHOD_NAME = 'marketplace_credit_card';

    /**
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return UnzerConfig::PAYMENT_PROVIDER_NAME . DIRECTORY_SEPARATOR . static::PAYMENT_METHOD_NAME;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return 'unzerMarketplaceCreditCard';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'unzerMarketplaceCreditCard';
    }
}
