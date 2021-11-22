<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiCreateMetadataRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerMetadataTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerMetadataMapperInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerMetadataAdapter extends UnzerAbstractApiAdapter implements UnzerMetadataAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerMetadataMapperInterface
     */
    protected $unzerMetadataMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerMetadataMapperInterface $unzerMetadataMapper
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerMetadataMapperInterface $unzerMetadataMapper
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerMetadataMapper = $unzerMetadataMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerMetadataTransfer $unzerMetadataTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerMetadataTransfer
     */
    public function createMetadata(
        UnzerMetadataTransfer $unzerMetadataTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): UnzerMetadataTransfer {
        $unzerApiCreateMetadataRequestTransfer = $this->unzerMetadataMapper
            ->mapUnzerMetadataTransferToUnzerApiCreateMetadataRequestTransfer(
                $unzerMetadataTransfer,
                new UnzerApiCreateMetadataRequestTransfer(),
            );

        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setCreateMetadataRequest($unzerApiCreateMetadataRequestTransfer)
            ->setUnzerKeypair($unzerKeypairTransfer);

        $unzerApiResponseTransfer = $this->unzerApiFacade->performCreateMetadataApiCall($unzerApiRequestTransfer);
        $this->assertSuccessResponse($unzerApiResponseTransfer);
        $unzerApiCreateMetadataResponseTransfer = $unzerApiResponseTransfer->getCreateMetadataResponseOrFail();

        return $this->unzerMetadataMapper
            ->mapUnzerApiCreateMetadataResponseTransferToUnzerMetadataTransfer(
                $unzerApiCreateMetadataResponseTransfer,
                $unzerMetadataTransfer,
            );
    }
}
