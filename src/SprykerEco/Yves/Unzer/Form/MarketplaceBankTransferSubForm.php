<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Unzer\Form;

use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarketplaceBankTransferSubForm extends AbstractUnzerSubForm
{
    public const PAYMENT_METHOD = 'marketplace-bank-transfer';

    /**
     * @return string
     */
    public function getName()
    {
        return 'unzerMarketplaceBankTransfer';
    }

    /**
     * @return string
     */
    public function getPropertyPath()
    {
        return 'unzerMarketplaceBankTransfer';
    }

    /**
     * @return string
     */
    public function getTemplatePath()
    {
        return 'unzer' . '/' . 'marketplaceBankTransfer';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UnzerPaymentTransfer::class,
        ])->setRequired(static::OPTIONS_FIELD_NAME);
    }
}
