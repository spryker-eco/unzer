<?php

namespace SprykerEco\Client\Unzer;

use Pyz\Client\Unzer\Zed\UnzerStub;
use Spryker\Client\Kernel\AbstractFactory;

class UnzerFactory extends AbstractFactory
{

    /**
     * @return \Pyz\Client\Unzer\Zed\UnzerStubInterface
     */
    public function createZedStub()
    {
        return new UnzerStub($this->getZedRequestClient());
    }

    /**
     * @return \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected function getZedRequestClient()
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::CLIENT_ZED_REQUEST);
    }

}
