<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Dependency;

class UnzerToVaultFacadeBridge implements UnzerToVaultFacadeInterface
{
    /**
     * @var \Spryker\Zed\Vault\Business\VaultFacadeInterface
     */
    protected $vaultFacade;

    /**
     * @param \Spryker\Zed\Vault\Business\VaultFacadeInterface $vaultFacade
     */
    public function __construct($vaultFacade)
    {
        $this->vaultFacade = $vaultFacade;
    }

    /**
     * @param string $dataType
     * @param string $dataKey
     * @param string $data
     *
     * @return bool
     */
    public function store(string $dataType, string $dataKey, string $data): bool
    {
        return $this->vaultFacade->store($dataType, $dataKey, $data);
    }

    /**
     * @param string $dataType
     * @param string $dataKey
     *
     * @return string|null
     */
    public function retrieve(string $dataType, string $dataKey): ?string
    {
        return $this->vaultFacade->retrieve($dataType, $dataKey);
    }
}
