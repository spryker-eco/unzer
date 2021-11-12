<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerMetadataTransfer;

interface UnzerMetadataAdapterInterface
{
    /**
     * @param UnzerMetadataTransfer $unzerMetadataTransfer
     *
     * @return UnzerMetadataTransfer
     */
    public function createMetadata(UnzerMetadataTransfer $unzerMetadataTransfer): UnzerMetadataTransfer;
}
