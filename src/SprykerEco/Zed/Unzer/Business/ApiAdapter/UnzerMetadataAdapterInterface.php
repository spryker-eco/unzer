<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerMetadataTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerMetadataAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerMetadataTransfer $unzerMetadataTransfer
     * @param UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerMetadataTransfer
     */
    public function createMetadata(
        UnzerMetadataTransfer $unzerMetadataTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): UnzerMetadataTransfer;
}
