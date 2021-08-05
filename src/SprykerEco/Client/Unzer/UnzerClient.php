<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

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
