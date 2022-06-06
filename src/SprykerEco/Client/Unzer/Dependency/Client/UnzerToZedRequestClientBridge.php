<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\Unzer\Dependency\Client;

use Spryker\Shared\Kernel\Transfer\TransferInterface;

class UnzerToZedRequestClientBridge implements UnzerToZedRequestClientInterface
{
    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedRequestClient
     */
    public function __construct($zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * @param string $url
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $object
     * @param array<mixed>|null $requestOptions
     *
     * @return \Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function call(string $url, TransferInterface $object, ?array $requestOptions = null): TransferInterface
    {
        return $this->zedRequestClient->call($url, $object, $requestOptions);
    }
}
