<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\KeypairResolver;

use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;

interface UnzerKeypairResolverInterface
{
    /**
     * @param string $merchantReference
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    public function getUnzerKeypairByMerchantReferenceAndStore(
        string $merchantReference,
        StoreTransfer $storeTransfer
    ): UnzerKeypairTransfer;

    /**
     * @param string $unzerPrimaryKeypairId
     *
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    public function getUnzerKeypairByKeypairId(string $unzerPrimaryKeypairId): UnzerKeypairTransfer;
}
