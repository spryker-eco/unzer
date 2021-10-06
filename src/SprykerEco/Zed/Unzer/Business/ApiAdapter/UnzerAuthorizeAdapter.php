<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerAuthorizePaymentMapperInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerAuthorizeAdapter extends UnzerAbstractApiAdapter implements UnzerAuthorizeAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerAuthorizePaymentMapperInterface
     */
    protected $unzerAuthorizePaymentMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerAuthorizePaymentMapperInterface $unzerAuthorizePaymentMapper
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerAuthorizePaymentMapperInterface $unzerAuthorizePaymentMapper,
        UnzerConfig $unzerConfig
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerAuthorizePaymentMapper = $unzerAuthorizePaymentMapper;
        $this->unzerConfig = $unzerConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function authorizePayment(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        if ($unzerPaymentTransfer->getIsMarketplaceOrFail()) {
            return $this->performMarketplaceAuthorize($unzerPaymentTransfer);
        }

        return $this->performRegularAuthorize($unzerPaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function performMarketplaceAuthorize(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        $unzerApiRequestTransfer = new UnzerApiRequestTransfer();

        $unzerApiMarketplaceAuthorizeRequestTransfer = $this
            ->unzerAuthorizePaymentMapper
            ->mapUnzerPaymentTransferToUnzerApiMarketplaceAuthorizeRequestTransfer(
                $unzerPaymentTransfer,
                new UnzerApiMarketplaceAuthorizeRequestTransfer()
            );

        $unzerApiRequestTransfer->setMarketplaceAuthorizeRequest($unzerApiMarketplaceAuthorizeRequestTransfer);
        $unzerApiResponseTransfer = $this->unzerApiFacade->performMarketplaceAuthorizeApiCall($unzerApiRequestTransfer);
        $this->checkSuccessResponse($unzerApiResponseTransfer);

        $unzerApiMarketplaceAuthorizeResponseTransfer = $unzerApiResponseTransfer->getMarketplaceAuthorizeResponseOrFail();

        return $this
            ->unzerAuthorizePaymentMapper
            ->mapUnzerApiMarketplaceAuthorizeResponseTransferToUnzerPaymentTransfer(
                $unzerApiMarketplaceAuthorizeResponseTransfer,
                $unzerPaymentTransfer
            );
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function performRegularAuthorize(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        return $unzerPaymentTransfer;
    }
}