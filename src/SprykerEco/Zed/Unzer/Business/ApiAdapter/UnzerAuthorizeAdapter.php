<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerAuthorizePaymentMapperInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;
use SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface;

class UnzerAuthorizeAdapter extends UnzerAbstractApiAdapter implements UnzerAuthorizeAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface
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
     * @param \SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerAuthorizePaymentMapperInterface $unzerAuthorizePaymentMapper
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(
        UnzerApiFacadeInterface $unzerApiFacade,
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
        $isMarketplace = $unzerPaymentTransfer->getIsMarketplaceOrFail();
        if ($isMarketplace) {
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
