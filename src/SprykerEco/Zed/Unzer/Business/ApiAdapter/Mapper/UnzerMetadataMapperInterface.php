<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiCreateCustomerResponseTransfer;
use Generated\Shared\Transfer\UnzerApiCreateMetadataRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreateMetadataResponseTransfer;
use Generated\Shared\Transfer\UnzerMetadataTransfer;

interface UnzerMetadataMapperInterface
{
    /**
     * @param UnzerMetadataTransfer $unzerMetadataTransfer
     * @param UnzerApiCreateMetadataRequestTransfer $unzerApiCreateMetadataRequestTransfer
     *
     * @return UnzerApiCreateMetadataRequestTransfer
     */
    public function mapUnzerMetadataTransferToUnzerApiCreateMetadataRequestTransfer(
        UnzerMetadataTransfer $unzerMetadataTransfer,
        UnzerApiCreateMetadataRequestTransfer $unzerApiCreateMetadataRequestTransfer
    ): UnzerApiCreateMetadataRequestTransfer;

    /**
     * @param UnzerApiCreateCustomerResponseTransfer $unzerApiCreateMetadataResponseTransfer
     * @param UnzerMetadataTransfer $unzerMetadataTransfer
     *
     * @return UnzerMetadataTransfer
     */
    public function mapUnzerApiCreateMetadataResponseTransferToUnzerMetadataTransfer(
        UnzerApiCreateMetadataResponseTransfer $unzerApiCreateMetadataResponseTransfer,
        UnzerMetadataTransfer $unzerMetadataTransfer
    ): UnzerMetadataTransfer;
}
