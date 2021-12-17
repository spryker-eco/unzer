<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Reader;

use SprykerEco\Zed\Unzer\Dependency\UnzerToVaultFacadeInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerVaultReader implements UnzerVaultReaderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToVaultFacadeInterface
     */
    protected $vaultFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToVaultFacadeInterface $vaultFacade
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(
        UnzerToVaultFacadeInterface $vaultFacade,
        UnzerConfig $unzerConfig
    ) {
        $this->vaultFacade = $vaultFacade;
        $this->unzerConfig = $unzerConfig;
    }

    /**
     * @param string $keypairId
     *
     * @return string|null
     */
    public function retrieveUnzerPrivateKey(string $keypairId): ?string
    {
        $dataType = $this->unzerConfig->getVaultDataType();

        return $this->vaultFacade->retrieve($dataType, $keypairId);
    }
}
