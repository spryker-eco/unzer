<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiCreateMetadataRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerMetadataTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerMetadataMapperInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerMetadataAdapter extends UnzerAbstractApiAdapter implements UnzerMetadataAdapterInterface
{
    /**
     * @var UnzerMetadataMapperInterface
     */
    protected $unzerMetadataMapper;

    /**
     * @var UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param UnzerMetadataMapperInterface $unzerMetadataMapper
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerMetadataMapperInterface $unzerMetadataMapper
    )
    {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerMetadataMapper = $unzerMetadataMapper;
    }

    /**
     * @inheritDoc
     */
    public function createMetadata(UnzerMetadataTransfer $unzerMetadataTransfer): UnzerMetadataTransfer
    {
        $unzerApiCreateMetadataRequestTransfer = $this->unzerMetadataMapper
            ->mapUnzerMetadataTransferToUnzerApiCreateMetadataRequestTransfer(
                $unzerMetadataTransfer,
                new UnzerApiCreateMetadataRequestTransfer(),
            );

        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setCreateMetadataRequest($unzerApiCreateMetadataRequestTransfer);

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
