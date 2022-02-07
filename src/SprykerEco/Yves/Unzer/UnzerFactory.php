<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer;

use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use SprykerEco\Yves\Unzer\Dependency\UnzerToQuoteClientInterface;
use SprykerEco\Yves\Unzer\Form\DataProvider\MarketplaceBankTransferFormDataProvider;
use SprykerEco\Yves\Unzer\Form\DataProvider\MarketplaceCreditCardFormDataProvider;
use SprykerEco\Yves\Unzer\Form\MarketplaceBankTransferSubForm;
use SprykerEco\Yves\Unzer\Form\MarketplaceCreditCardSubForm;
use SprykerEco\Yves\Unzer\Handler\UnzerHandler;
use SprykerEco\Yves\Unzer\Handler\UnzerHandlerInterface;

class UnzerFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMarketplaceBankTransferFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MarketplaceBankTransferFormDataProvider(
            $this->getQuoteClient(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMarketplaceCreditCardFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MarketplaceCreditCardFormDataProvider(
            $this->getQuoteClient(),
        );
    }

    /**
     * @return \SprykerEco\Yves\Unzer\Dependency\UnzerToQuoteClientInterface
     */
    public function getQuoteClient(): UnzerToQuoteClientInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::CLIENT_QUOTE);
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMarketplaceBankTransferSubForm(): SubFormInterface
    {
        return new MarketplaceBankTransferSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMarketplaceCreditCardSubForm(): SubFormInterface
    {
        return new MarketplaceCreditCardSubForm();
    }

    /**
     * @return \SprykerEco\Yves\Unzer\Handler\UnzerHandlerInterface
     */
    public function createUnzerHandler(): UnzerHandlerInterface
    {
        return new UnzerHandler();
    }
}
