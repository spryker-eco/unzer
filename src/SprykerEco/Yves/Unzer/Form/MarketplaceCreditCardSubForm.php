<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Form;

use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarketplaceCreditCardSubForm extends AbstractUnzerSubForm
{
    /**
     * @var string
     */
    public const PAYMENT_METHOD = 'marketplace-credit-card';

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'unzerMarketplaceCreditCard';
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
    public function getTemplatePath(): string
    {
        return 'unzer' . '/' . 'marketplaceCreditCard';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UnzerPaymentTransfer::class,
        ])->setRequired(static::OPTIONS_FIELD_NAME);
    }
}
