<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiCreateBasketRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerBasketMapperInterface;
use SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface;

class UnzerBasketAdapter extends UnzerAbstractApiAdapter implements UnzerBasketAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerBasketMapperInterface
     */
    protected $unzerBasketMapper;

    /**
     * @param \SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerBasketMapperInterface $unzerBasketMapper
     */
    public function __construct(
        UnzerApiFacadeInterface $unzerApiFacade,
        UnzerBasketMapperInterface $unzerBasketMapper
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerBasketMapper = $unzerBasketMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    public function createBasket(UnzerBasketTransfer $unzerBasketTransfer): UnzerBasketTransfer
    {
        $createBasketRequest = $this
            ->unzerBasketMapper
            ->mapUnzerBasketTransferToUnzerApiCreateBasketRequestTransfer(
                $unzerBasketTransfer,
                new UnzerApiCreateBasketRequestTransfer()
            );

        $unzerApiRequest = new UnzerApiRequestTransfer();
        $unzerApiRequest->setCreateBasketRequest($createBasketRequest);

        $unzerApiResponse = $this->unzerApiFacade->performCreateBasketApiCall($unzerApiRequest);
        $this->checkSuccessResponse($unzerApiResponse);
        $createBasketResponseTransfer = $unzerApiResponse->getCreateBasketResponseOrFail();

        return $this->unzerBasketMapper
            ->mapUnzerApiCreateBasketResponseTransferToUnzerBasketTransfer(
                $createBasketResponseTransfer,
                $unzerBasketTransfer
            );
    }
}
