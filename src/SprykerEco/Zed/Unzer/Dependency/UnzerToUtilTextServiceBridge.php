<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Dependency;

class UnzerToUtilTextServiceBridge implements UnzerToUtilTextServiceInterface
{
    /**
     * @var \Spryker\Service\UtilText\UtilTextServiceInterface
     */
    protected $utilTextService;

    /**
     * @param \Spryker\Service\UtilText\UtilTextServiceInterface $utilTextService
     */
    public function __construct($utilTextService)
    {
        $this->utilTextService = $utilTextService;
    }

    /**
     * @param string $prefix
     * @param bool $moreEntropy
     *
     * @return string
     */
    public function generateUniqueId(string $prefix = '', bool $moreEntropy = false): string
    {
        return $this->utilTextService->generateUniqueId($prefix, $moreEntropy);
    }
}
