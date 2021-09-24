<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiCreatePaymentResourceRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerPaymentResourceMapperInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerPaymentResourceAdapter extends UnzerAbstractApiAdapter implements UnzerPaymentResourceAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerPaymentResourceMapperInterface
     */
    protected $unzerPaymentResourceMapper;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerPaymentResourceMapperInterface $unzerPaymentResourceMapper
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerPaymentResourceMapperInterface $unzerPaymentResourceMapper
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerPaymentResourceMapper = $unzerPaymentResourceMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentResourceTransfer
     */
    public function createPaymentResource(UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer): UnzerPaymentResourceTransfer
    {
        $createPaymentResourceRequest = $this
            ->unzerPaymentResourceMapper
            ->mapUnzerPaymentResourceTransferToUnzerApiCreatePaymentResourceRequestTransfer(
                $unzerPaymentResourceTransfer,
                new UnzerApiCreatePaymentResourceRequestTransfer()
            );

        $unzerApiRequest = new UnzerApiRequestTransfer();
        $unzerApiRequest->setCreatePaymentResourceRequest($createPaymentResourceRequest);

        $unzerApiResponse = $this->unzerApiFacade->performCreatePaymentResourceApiCall($unzerApiRequest);
        $this->checkSuccessResponse($unzerApiResponse);
        $createPaymentResourceResponse = $unzerApiResponse->getCreatePaymentResourceResponseOrFail();

        return $this->
        unzerPaymentResourceMapper
            ->mapUnzerApiCreatePaymentResourceTransferResponseToUnzerPaymentResourceTransfer(
                $createPaymentResourceResponse,
                $unzerPaymentResourceTransfer
            );
    }
}
