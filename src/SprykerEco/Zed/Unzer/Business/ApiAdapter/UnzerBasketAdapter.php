<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiCreateBasketRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
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
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    public function createBasket(UnzerBasketTransfer $unzerBasketTransfer, UnzerKeypairTransfer $unzerKeypairTransfer): UnzerBasketTransfer
    {
        $unzerApiRequestTransfer = $this->prepareUnzerApiRequestTransfer($unzerBasketTransfer, $unzerKeypairTransfer);
        $unzerApiResponseTransfer = $this->unzerApiFacade->performCreateBasketApiCall($unzerApiRequestTransfer);

        return $this->parseUnzerApiResponseTransfer($unzerApiResponseTransfer, $unzerBasketTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    public function createMarketplaceBasket(UnzerBasketTransfer $unzerBasketTransfer, UnzerKeypairTransfer $unzerKeypairTransfer): UnzerBasketTransfer
    {
        $unzerApiRequestTransfer = $this->prepareUnzerApiRequestTransfer($unzerBasketTransfer, $unzerKeypairTransfer);
        $unzerApiResponseTransfer = $this->unzerApiFacade->performCreateMarketplaceBasketApiCall($unzerApiRequestTransfer);

        return $this->parseUnzerApiResponseTransfer($unzerApiResponseTransfer, $unzerBasketTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiRequestTransfer
     */
    protected function prepareUnzerApiRequestTransfer(
        UnzerBasketTransfer $unzerBasketTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): UnzerApiRequestTransfer {
        $unzerApiCreateBasketRequestTransfer = $this->unzerBasketMapper
            ->mapUnzerBasketTransferToUnzerApiCreateBasketRequestTransfer(
                $unzerBasketTransfer,
                new UnzerApiCreateBasketRequestTransfer(),
            );

        return (new UnzerApiRequestTransfer())
            ->setCreateBasketRequest($unzerApiCreateBasketRequestTransfer)
            ->setUnzerKeypair($unzerKeypairTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiResponseTransfer $unzerApiResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    protected function parseUnzerApiResponseTransfer(
        UnzerApiResponseTransfer $unzerApiResponseTransfer,
        UnzerBasketTransfer $unzerBasketTransfer
    ): UnzerBasketTransfer {
        $this->assertSuccessResponse($unzerApiResponseTransfer);
        $createBasketResponseTransfer = $unzerApiResponseTransfer->getCreateBasketResponseOrFail();

        return $this->unzerBasketMapper
            ->mapUnzerApiCreateBasketResponseTransferToUnzerBasketTransfer(
                $createBasketResponseTransfer,
                $unzerBasketTransfer,
            );
    }
}
