<?php

namespace SprykerEco\Yves\Unzer\Form;

use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use SprykerEco\Shared\Unzer\UnzerConfig;

abstract class AbstractUnzerSubForm extends AbstractSubFormType implements SubFormInterface, SubFormProviderNameInterface
{
    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return UnzerConfig::PROVIDER_NAME;
    }
}
