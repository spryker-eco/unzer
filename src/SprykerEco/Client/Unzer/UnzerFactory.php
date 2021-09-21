<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\Unzer;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;
use SprykerEco\Client\Unzer\Zed\UnzerStub;
use SprykerEco\Client\Unzer\Zed\UnzerStubInterface;

class UnzerFactory extends AbstractFactory
{
    /**
     * @return \SprykerEco\Client\Unzer\Zed\UnzerStubInterface
     */
    public function createZedStub(): UnzerStubInterface
    {
        return new UnzerStub($this->getZedRequestClient());
    }

    /**
     * @return \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    public function getZedRequestClient(): ZedRequestClientInterface
    {
        /** @var \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedRequestClient */
        $zedRequestClient = $this->getProvidedDependency(UnzerDependencyProvider::CLIENT_ZED_REQUEST);

        return $zedRequestClient;
    }
}
