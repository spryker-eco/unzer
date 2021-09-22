<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\Unzer;

use Spryker\Client\Kernel\AbstractFactory;
use SprykerEco\Client\Unzer\Dependency\Client\UnzerToZedRequestClientInterface;
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
     * @return \SprykerEco\Client\Unzer\Dependency\Client\UnzerToZedRequestClientInterface
     */
    public function getZedRequestClient(): UnzerToZedRequestClientInterface
    {
        /** @var \SprykerEco\Client\Unzer\Dependency\Client\UnzerToZedRequestClientInterface $zedRequestClient */
        $zedRequestClient = $this->getProvidedDependency(UnzerDependencyProvider::CLIENT_ZED_REQUEST);

        return $zedRequestClient;
    }
}
