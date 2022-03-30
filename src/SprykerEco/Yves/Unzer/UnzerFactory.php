<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer;

use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use SprykerEco\Client\Unzer\UnzerClientInterface;
use SprykerEco\Yves\Unzer\Dependency\Client\UnzerToQuoteClientInterface;
use SprykerEco\Yves\Unzer\Dependency\Service\UnzerToUtilEncodingServiceInterface;
use SprykerEco\Yves\Unzer\Form\BankTransferSubForm;
use SprykerEco\Yves\Unzer\Form\CreditCardSubForm;
use SprykerEco\Yves\Unzer\Form\DataProvider\BankTransferFormDataProvider;
use SprykerEco\Yves\Unzer\Form\DataProvider\CreditCardFormDataProvider;
use SprykerEco\Yves\Unzer\Form\DataProvider\MarketplaceBankTransferFormDataProvider;
use SprykerEco\Yves\Unzer\Form\DataProvider\MarketplaceCreditCardFormDataProvider;
use SprykerEco\Yves\Unzer\Form\DataProvider\MarketplaceSofortFormDataProvider;
use SprykerEco\Yves\Unzer\Form\DataProvider\SofortFormDataProvider;
use SprykerEco\Yves\Unzer\Form\MarketplaceBankTransferSubForm;
use SprykerEco\Yves\Unzer\Form\MarketplaceCreditCardSubForm;
use SprykerEco\Yves\Unzer\Form\MarketplaceSofortSubForm;
use SprykerEco\Yves\Unzer\Form\SofortSubForm;
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
    public function createMarketplaceSofortFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MarketplaceSofortFormDataProvider(
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
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createBankTransferFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new BankTransferFormDataProvider(
            $this->getQuoteClient(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createSofortFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new SofortFormDataProvider(
            $this->getQuoteClient(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createCreditCardFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new CreditCardFormDataProvider(
            $this->getQuoteClient(),
        );
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
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createCreditCardSubForm(): SubFormInterface
    {
        return new CreditCardSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMarketplaceSofortSubForm(): SubFormInterface
    {
        return new MarketplaceSofortSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createBankTransferSubForm(): SubFormInterface
    {
        return new BankTransferSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createSofortSubForm(): SubFormInterface
    {
        return new SofortSubForm();
    }

    /**
     * @return \SprykerEco\Yves\Unzer\Handler\UnzerHandlerInterface
     */
    public function createUnzerHandler(): UnzerHandlerInterface
    {
        return new UnzerHandler();
    }

    /**
     * @return \SprykerEco\Yves\Unzer\Dependency\Client\UnzerToQuoteClientInterface
     */
    public function getQuoteClient(): UnzerToQuoteClientInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::CLIENT_QUOTE);
    }

    /**
     * @return \SprykerEco\Client\Unzer\UnzerClientInterface
     */
    public function getUnzerClient(): UnzerClientInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::CLIENT_UNZER);
    }

    /**
     * @return \SprykerEco\Yves\Unzer\Dependency\Service\UnzerToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): UnzerToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::SERVICE_UTIL_ENCODING);
    }
}
