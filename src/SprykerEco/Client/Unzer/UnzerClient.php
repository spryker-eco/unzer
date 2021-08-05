<?php

namespace SprykerEco\Client\Unzer;

use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \Pyz\Client\Unzer\UnzerFactory getFactory()
 */
class UnzerClient extends AbstractClient implements UnzerClientInterface
{

    /**
     * @return \Pyz\Client\Unzer\Zed\UnzerStubInterface
     */
    protected function getZedStub()
    {
        return $this->getFactory()->createZedStub();
    }

}
