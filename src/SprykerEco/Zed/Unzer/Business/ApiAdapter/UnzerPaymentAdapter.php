<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiGetPaymentRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerPaymentAdapter implements UnzerPaymentAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected UnzerToUnzerApiFacadeInterface $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMapperInterface
     */
    protected UnzerGetPaymentMapperInterface $unzerPaymentMapper;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMapperInterface $unzerPaymentMapper
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
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

        return $this->getStandardPaymentInfo($unzerPaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function getMarketplacePaymentInfo(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        $unzerApiRequestTransfer = $this->createUnzerApiRequestTransferWithGetPaymentRequest($unzerPaymentTransfer);
        $unzerApiResponseTransfer = $this->unzerApiFacade->performMarketplaceGetPaymentApiCall($unzerApiRequestTransfer);

        if ($unzerApiResponseTransfer->getIsSuccessful()) {
            return $this->unzerPaymentMapper->mapUnzerApiGetPaymentResponseTransferToUnzerPaymentTransfer(
                $unzerApiResponseTransfer->getGetPaymentResponseOrFail(),
                $unzerPaymentTransfer,
            );
        }

        return $this->unzerPaymentMapper
            ->mapUnzerApiErrorResponseTransferToUnzerPaymentTransfer(
                $unzerApiResponseTransfer->getErrorResponseOrFail(),
                $unzerPaymentTransfer,
            );
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function getStandardPaymentInfo(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        $unzerApiRequestTransfer = $this->createUnzerApiRequestTransferWithGetPaymentRequest($unzerPaymentTransfer);
        $unzerApiResponseTransfer = $this->unzerApiFacade->performGetPaymentApiCall($unzerApiRequestTransfer);

        if ($unzerApiResponseTransfer->getIsSuccessful()) {
            return $this->unzerPaymentMapper->mapUnzerApiGetPaymentResponseTransferToUnzerPaymentTransfer(
                $unzerApiResponseTransfer->getGetPaymentResponseOrFail(),
                $unzerPaymentTransfer,
            );
        }

        return $this->unzerPaymentMapper
            ->mapUnzerApiErrorResponseTransferToUnzerPaymentTransfer(
                $unzerApiResponseTransfer->getErrorResponseOrFail(),
                $unzerPaymentTransfer,
            );
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiRequestTransfer
     */
    protected function createUnzerApiRequestTransferWithGetPaymentRequest(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerApiRequestTransfer
    {
        $unzerApiGetPaymentRequestTransfer = $this->unzerPaymentMapper
            ->mapUnzerPaymentTransferToUnzerApiGetPaymentRequestTransfer(
                $unzerPaymentTransfer,
                new UnzerApiGetPaymentRequestTransfer(),
            );

        return (new UnzerApiRequestTransfer())
            ->setGetPaymentRequest($unzerApiGetPaymentRequestTransfer)
            ->setUnzerKeypair($unzerPaymentTransfer->getUnzerKeypairOrFail());
    }
}
