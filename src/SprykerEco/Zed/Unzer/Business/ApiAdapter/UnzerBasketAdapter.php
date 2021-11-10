<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiCreateBasketRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerBasketMapperInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerBasketAdapter extends UnzerAbstractApiAdapter implements UnzerBasketAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerBasketMapperInterface
     */
    protected $unzerBasketMapper;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerBasketMapperInterface $unzerBasketMapper
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
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
        $unzerApiCreateBasketRequestTransfer = $this->unzerBasketMapper
            ->mapUnzerBasketTransferToUnzerApiCreateBasketRequestTransfer(
                $unzerBasketTransfer,
                new UnzerApiCreateBasketRequestTransfer()
            );

        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setCreateBasketRequest($unzerApiCreateBasketRequestTransfer);

        $unzerApiResponseTransfer = $this->unzerApiFacade->performCreateBasketApiCall($unzerApiRequestTransfer);
        $this->assertSuccessResponse($unzerApiResponseTransfer);
        $createBasketResponseTransfer = $unzerApiResponseTransfer->getCreateBasketResponseOrFail();

        return $this->unzerBasketMapper
            ->mapUnzerApiCreateBasketResponseTransferToUnzerBasketTransfer(
                $createBasketResponseTransfer,
                $unzerBasketTransfer
            );
    }
}
