<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Reader;

interface UnzerVaultReaderInterface
{
    /**
     * @param string $keypairId
     *
     * @return string|null
     */
    public function retrieveUnzerPrivateKey(string $keypairId): ?string;
}
