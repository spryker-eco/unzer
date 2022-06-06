<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Writer;

use SprykerEco\Zed\Unzer\Dependency\UnzerToVaultFacadeInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerVaultWriter implements UnzerVaultWriterInterface
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
     * @param string $unzerKeypairId
     * @param string $unzerPrivateKey
     *
     * @return bool
     */
    public function storeUnzerPrivateKey(string $unzerKeypairId, string $unzerPrivateKey): bool
    {
        $dataType = $this->unzerConfig->getVaultDataType();

        return $this->vaultFacade->store($dataType, $unzerKeypairId, $unzerPrivateKey);
    }
}
