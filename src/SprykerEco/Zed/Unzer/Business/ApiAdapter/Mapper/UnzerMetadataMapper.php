<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiCreateMetadataRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreateMetadataResponseTransfer;
use Generated\Shared\Transfer\UnzerMetadataTransfer;

class UnzerMetadataMapper implements UnzerMetadataMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerMetadataTransfer $unzerMetadataTransfer
     * @param \Generated\Shared\Transfer\UnzerApiCreateMetadataRequestTransfer $unzerApiCreateMetadataRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiCreateMetadataRequestTransfer
     */
    public function mapUnzerMetadataTransferToUnzerApiCreateMetadataRequestTransfer(
        UnzerMetadataTransfer $unzerMetadataTransfer,
        UnzerApiCreateMetadataRequestTransfer $unzerApiCreateMetadataRequestTransfer
    ): UnzerApiCreateMetadataRequestTransfer {
        return $unzerApiCreateMetadataRequestTransfer->fromArray($unzerMetadataTransfer->toArray(), true);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiCreateCustomerResponseTransfer $unzerApiCreateMetadataResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerMetadataTransfer $unzerMetadataTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerMetadataTransfer
     */
    public function mapUnzerApiCreateMetadataResponseTransferToUnzerMetadataTransfer(
        UnzerApiCreateMetadataResponseTransfer $unzerApiCreateMetadataResponseTransfer,
        UnzerMetadataTransfer $unzerMetadataTransfer
    ): UnzerMetadataTransfer {
        return $unzerMetadataTransfer
            ->setId($unzerApiCreateMetadataResponseTransfer->getId());
    }
}
