<?php

namespace SprykerEco\Zed\Unzer\Business\Payment\KeypairResolver;

use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;

interface UnzerKeypairResolverInterface
{
    /**
     * @param string $merchantReference
     * @param StoreTransfer $storeTransfer
     *
     * @return UnzerKeypairTransfer
     */
    public function getUnzerKeypairByMerchantReferenceAndStore(
        string $merchantReference,
        StoreTransfer $storeTransfer
    ): UnzerKeypairTransfer;

    /**
     * @param string $unzerPrimaryKeypairId
     *
     * @return UnzerKeypairTransfer
     */
    public function getUnzerKeypairByKeypairId(string $unzerPrimaryKeypairId): UnzerKeypairTransfer;
}
