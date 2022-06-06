<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Form;

use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use SprykerEco\Shared\Unzer\UnzerConfig;

abstract class AbstractUnzerSubForm extends AbstractSubFormType implements SubFormInterface, SubFormProviderNameInterface
{
    /**
     * @var string
     */
    protected const TEMPLATE_VIEW_PATH = '';

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return UnzerConfig::PAYMENT_PROVIDER_NAME;
    }

    /**
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return UnzerConfig::PAYMENT_PROVIDER_NAME . DIRECTORY_SEPARATOR . static::TEMPLATE_VIEW_PATH;
    }
}
