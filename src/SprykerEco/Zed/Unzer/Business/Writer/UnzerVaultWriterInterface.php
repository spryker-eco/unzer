<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Writer;

interface UnzerVaultWriterInterface
{
    /**
     * @param string $unzerKeypairId
     * @param string $unzerPrivateKey
     *
     * @return bool
     */
    public function storeUnzerPrivateKey(string $unzerKeypairId, string $unzerPrivateKey): bool;
}
