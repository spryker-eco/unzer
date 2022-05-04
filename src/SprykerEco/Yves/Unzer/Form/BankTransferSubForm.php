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

/**
 * @method \SprykerEco\Yves\Unzer\UnzerConfig getConfig()
 */
class BankTransferSubForm extends AbstractUnzerSubForm
{
    /**
     * @var string
     */
    protected const TEMPLATE_VIEW_PATH = 'views/bank-transfer/bank-transfer';

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return PaymentTransfer::UNZER_BANK_TRANSFER;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return PaymentTransfer::UNZER_BANK_TRANSFER;
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

    /**
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return UnzerConfig::PAYMENT_PROVIDER_NAME . DIRECTORY_SEPARATOR . static::TEMPLATE_VIEW_PATH;
    }
}
