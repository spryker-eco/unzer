<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiGetPaymentRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMapperInterface;
use SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface;

class UnzerPaymentAdapter extends UnzerAbstractApiAdapter implements UnzerPaymentAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @param \SprykerEco\Zed\UnzerApi\Business\UnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMapperInterface $unzerPaymentMapper
     */
    public function __construct(
        UnzerApiFacadeInterface $unzerApiFacade,
        UnzerGetPaymentMapperInterface $unzerPaymentMapper
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerPaymentMapper = $unzerPaymentMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function getPaymentInfo(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        if ($unzerPaymentTransfer->getIsMarketplace()) {
            return $this->getMarketplacePaymentInfo($unzerPaymentTransfer);
        }

        return $this->getRegularPaymentInfo($unzerPaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function getMarketplacePaymentInfo(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        $unzerApiPaymentRequestTransfer = $this->unzerPaymentMapper
            ->mapUnzerPaymentTransferToUnzerApiGetPaymentRequestTransfer(
                $unzerPaymentTransfer,
                new UnzerApiGetPaymentRequestTransfer()
            );
        $unzerApiRequestTransfer = new UnzerApiRequestTransfer();
        $unzerApiRequestTransfer->setGetPaymentRequest($unzerApiPaymentRequestTransfer);

        $unzerApiResponseTransfer = $this->unzerApiFacade->performMarketplaceGetPaymentApiCall($unzerApiRequestTransfer);
        $this->checkSuccessResponse($unzerApiResponseTransfer);

        $unzerApiGetPaymentResponse = $unzerApiResponseTransfer->getGetPaymentResponseOrFail();

        return $this->unzerPaymentMapper->mapUnzerApiGetPaymentResponseTransferToUnzerPaymentTransfer(
            $unzerApiGetPaymentResponse,
            $unzerPaymentTransfer
        );
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function getRegularPaymentInfo(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        $unzerApiPaymentRequestTransfer = $this->unzerPaymentMapper
            ->mapUnzerPaymentTransferToUnzerApiGetPaymentRequestTransfer(
                $unzerPaymentTransfer,
                new UnzerApiGetPaymentRequestTransfer()
            );
        $unzerApiRequestTransfer = new UnzerApiRequestTransfer();
        $unzerApiRequestTransfer->setGetPaymentRequest($unzerApiPaymentRequestTransfer);

        $unzerApiResponseTransfer = $this->unzerApiFacade->performGetPaymentApiCall($unzerApiRequestTransfer);
        $this->checkSuccessResponse($unzerApiResponseTransfer);

        $unzerApiGetPaymentResponse = $unzerApiResponseTransfer->getGetPaymentResponseOrFail();

        return $this->unzerPaymentMapper->mapUnzerApiGetPaymentResponseTransferToUnzerPaymentTransfer(
            $unzerApiGetPaymentResponse,
            $unzerPaymentTransfer
        );
    }
}
