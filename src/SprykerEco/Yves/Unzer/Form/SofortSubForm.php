<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Form;

use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @method \SprykerEco\Yves\Unzer\UnzerConfig getConfig()
 */
class SofortSubForm extends AbstractUnzerSubForm
{
    /**
     * @var string
     */
    protected const TEMPLATE_VIEW_PATH = 'views/sofort/sofort';

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return PaymentTransfer::UNZER_SOFORT;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return PaymentTransfer::UNZER_SOFORT;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => UnzerPaymentTransfer::class,
        ])->setRequired(static::OPTIONS_FIELD_NAME);
    }
}
