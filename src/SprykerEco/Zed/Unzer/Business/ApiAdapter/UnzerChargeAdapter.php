<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiChargeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerChargeMapperInterface;
use SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface;

class UnzerChargeAdapter extends UnzerAbstractApiAdapter implements UnzerChargeAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerChargeMapperInterface
     */
    protected $unzerChargeMapper;

    /**
     * @param \SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerChargeMapperInterface $unzerChargeMapper
     */
    public function __construct(
        UnzerApiFacadeInterface $unzerApiFacade,
        UnzerChargeMapperInterface $unzerChargeMapper
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerChargeMapper = $unzerChargeMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function chargePayment(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        $unzerApiRequestTransfer = $this->prepareChargeRequest($unzerPaymentTransfer);

        $unzerApiResponseTransfer = $this->performCharge($unzerApiRequestTransfer, $unzerPaymentTransfer);
        $this->checkSuccessResponse($unzerApiResponseTransfer);
        $unzerApiChargeResponseTransfer = $unzerApiResponseTransfer->getChargeResponseOrFail();

        return $this->unzerChargeMapper
            ->mapUnzerApiChargeResponseTransferToUnzerPaymentTransfer(
                $unzerApiChargeResponseTransfer,
                $unzerPaymentTransfer
            );
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function chargeAuthorizablePayment(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        $unzerApiRequestTransfer = $this->prepareChargeRequest($unzerPaymentTransfer);

        $unzerApiResponseTransfer = $this->performAuthorizableCharge($unzerApiRequestTransfer, $unzerPaymentTransfer);
        $this->checkSuccessResponse($unzerApiResponseTransfer);
        $unzerApiChargeResponseTransfer = $unzerApiResponseTransfer->getChargeResponseOrFail();

        return $this->unzerChargeMapper
            ->mapAuthorizableUnzerApiChargeResponseTransferToUnzerPaymentTransfer(
                $unzerApiChargeResponseTransfer,
                $unzerPaymentTransfer
            );
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiRequestTransfer
     */
    protected function prepareChargeRequest(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerApiRequestTransfer
    {
        $unzerApiChargeRequestTransfer = $this
            ->unzerChargeMapper
            ->mapUnzerPaymentTransferToUnzerApiChargeRequestTransfer(
                $unzerPaymentTransfer,
                new UnzerApiChargeRequestTransfer()
            );

        $unzerApiRequestTransfer = new UnzerApiRequestTransfer();
        $unzerApiRequestTransfer->setChargeRequest($unzerApiChargeRequestTransfer);

        return $unzerApiRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiRequestTransfer $unzerApiRequestTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiResponseTransfer
     */
    protected function performCharge(UnzerApiRequestTransfer $unzerApiRequestTransfer, UnzerPaymentTransfer $unzerPaymentTransfer): UnzerApiResponseTransfer
    {
        $isMarketPlace = $unzerPaymentTransfer->getIsMarketplace();

        if ($isMarketPlace) {
            return $this->unzerApiFacade->performMarketplaceChargeApiCall($unzerApiRequestTransfer);
        }

        return $this->unzerApiFacade->performChargeApiCall($unzerApiRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiRequestTransfer $unzerApiRequestTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiResponseTransfer
     */
    protected function performAuthorizableCharge(
        UnzerApiRequestTransfer $unzerApiRequestTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerApiResponseTransfer {
        $isMarketPlace = $unzerPaymentTransfer->getIsMarketplace();

        if ($isMarketPlace) {
            return $this->unzerApiFacade->performMarketplaceAuthorizableChargeApiCall($unzerApiRequestTransfer);
        }

        return $this->unzerApiFacade->performAuthorizableChargeApiCall($unzerApiRequestTransfer);
    }
}
