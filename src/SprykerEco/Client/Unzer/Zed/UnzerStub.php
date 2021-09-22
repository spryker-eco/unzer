<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\Unzer\Zed;

use SprykerEco\Client\Unzer\Dependency\Client\UnzerToZedRequestClientInterface;

class UnzerStub implements UnzerStubInterface
{
    /**
     * @var \SprykerEco\Client\Unzer\Dependency\Client\UnzerToZedRequestClientInterface
     */
    protected $zedStubClient;

    /**
     * @param \SprykerEco\Client\Unzer\Dependency\Client\UnzerToZedRequestClientInterface $zedStubClient
     */
    public function __construct(UnzerToZedRequestClientInterface $zedStubClient)
    {
        $this->zedStubClient = $zedStubClient;
    }
}
