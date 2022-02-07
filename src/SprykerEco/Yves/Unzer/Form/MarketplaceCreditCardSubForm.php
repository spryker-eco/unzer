<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Unzer\Form;

use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarketplaceCreditCardSubForm extends AbstractUnzerSubForm
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
        return PaymentTransfer::UNZER_MARKETPLACE_CREDIT_CARD;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return PaymentTransfer::UNZER_MARKETPLACE_CREDIT_CARD;
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
