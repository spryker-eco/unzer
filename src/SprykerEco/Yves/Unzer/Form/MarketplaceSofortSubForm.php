<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Form;

use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarketplaceSofortSubForm extends SofortSubForm
{
    /**
     * @var string
     */
    protected const TEMPLATE_VIEW_PATH = 'views/marketplace-sofort/marketplace-sofort';

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return PaymentTransfer::UNZER_MARKETPLACE_SOFORT;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return PaymentTransfer::UNZER_MARKETPLACE_SOFORT;
    }

    /**
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return UnzerConfig::PAYMENT_PROVIDER_NAME . DIRECTORY_SEPARATOR . static::TEMPLATE_VIEW_PATH;
    }
}
