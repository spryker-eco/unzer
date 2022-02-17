<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Form;

use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use SprykerEco\Shared\Unzer\UnzerConfig;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreditCardSubForm extends AbstractUnzerSubForm
{
    /**
     * @var string
     */
    public const OPTION_PUBLIC_KEY = 'public_key';

    /**
     * @var string
     */
    protected const PAYMENT_METHOD_NAME = 'credit_card';

    /**
     * @var string
     */
    protected const FIELD_ID_PAYMENT = 'id';

    /**
     * @var string
     */
    protected const FIELD_PUBLIC_KEY = 'public_key';

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return PaymentTransfer::UNZER_CREDIT_CARD;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return PaymentTransfer::UNZER_CREDIT_CARD;
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
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addIdPaymentField($builder)
            ->addPublicKeyField($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIdPaymentField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ID_PAYMENT, HiddenType::class, [
            'required' => true,
            'property_path' => 'paymentResource.id',
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addPublicKeyField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_PUBLIC_KEY, HiddenType::class, [
            'required' => false,
            'mapped' => false,
            'data' => $options[SubFormInterface::OPTIONS_FIELD_NAME][static::OPTION_PUBLIC_KEY],
        ]);

        return $this;
    }

    /**
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return UnzerConfig::PAYMENT_PROVIDER_NAME . DIRECTORY_SEPARATOR . static::PAYMENT_METHOD_NAME;
    }
}
