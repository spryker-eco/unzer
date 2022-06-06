<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Form;

use Generated\Shared\Transfer\PaymentTransfer;

/**
 * @method \SprykerEco\Yves\Unzer\UnzerConfig getConfig()
 */
class MarketplaceCreditCardSubForm extends CreditCardSubForm
{
    /**
     * @var string
     */
    protected const TEMPLATE_VIEW_PATH = 'views/marketplace-credit-card/marketplace-credit-card';

    /**
     * @var string
     */
    protected const PAYMENT_METHOD_NAME = 'marketplace_credit_card';

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return PaymentTransfer::UNZER_MARKETPLACE_CREDIT_CARD;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return PaymentTransfer::UNZER_MARKETPLACE_CREDIT_CARD;
    }
}
