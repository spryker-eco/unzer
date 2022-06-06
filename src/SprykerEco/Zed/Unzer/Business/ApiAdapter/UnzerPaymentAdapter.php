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
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerPaymentAdapter implements UnzerPaymentAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface
     */
    protected $unzerApiAdapterResponseValidator;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMapperInterface $unzerPaymentMapper
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface $unzerApiAdapterResponseValidator
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerGetPaymentMapperInterface $unzerPaymentMapper,
        UnzerApiAdapterResponseValidatorInterface $unzerApiAdapterResponseValidator
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerPaymentMapper = $unzerPaymentMapper;
        $this->unzerApiAdapterResponseValidator = $unzerApiAdapterResponseValidator;
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
        $unzerApiGetPaymentRequestTransfer = $this->unzerPaymentMapper
            ->mapUnzerPaymentTransferToUnzerApiGetPaymentRequestTransfer(
                $unzerPaymentTransfer,
                new UnzerApiGetPaymentRequestTransfer(),
            );
        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setGetPaymentRequest($unzerApiGetPaymentRequestTransfer)
            ->setUnzerKeypair($unzerPaymentTransfer->getUnzerKeypairOrFail());

        $unzerApiResponseTransfer = $this->unzerApiFacade->performMarketplaceGetPaymentApiCall($unzerApiRequestTransfer);
        $this->unzerApiAdapterResponseValidator->assertSuccessResponse($unzerApiResponseTransfer);

        $unzerApiGetPaymentResponse = $unzerApiResponseTransfer->getGetPaymentResponseOrFail();

        return $this->unzerPaymentMapper->mapUnzerApiGetPaymentResponseTransferToUnzerPaymentTransfer(
            $unzerApiGetPaymentResponse,
            $unzerPaymentTransfer,
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
                new UnzerApiGetPaymentRequestTransfer(),
            );
        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setGetPaymentRequest($unzerApiPaymentRequestTransfer)
            ->setUnzerKeypair($unzerPaymentTransfer->getUnzerKeypairOrFail());

        $unzerApiResponseTransfer = $this->unzerApiFacade->performGetPaymentApiCall($unzerApiRequestTransfer);
        $this->unzerApiAdapterResponseValidator->assertSuccessResponse($unzerApiResponseTransfer);

        $unzerApiGetPaymentResponseTransfer = $unzerApiResponseTransfer->getGetPaymentResponseOrFail();

        return $this->unzerPaymentMapper->mapUnzerApiGetPaymentResponseTransferToUnzerPaymentTransfer(
            $unzerApiGetPaymentResponseTransfer,
            $unzerPaymentTransfer,
        );
    }
}
