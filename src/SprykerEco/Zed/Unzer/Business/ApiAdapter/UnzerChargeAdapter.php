<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiChargeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiChargeResponseTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use Generated\Shared\Transfer\UnzerChargeTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerChargeMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerChargeAdapter implements UnzerChargeAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerChargeMapperInterface
     */
    protected $unzerChargeMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface
     */
    protected $unzerApiAdapterResponseValidator;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerChargeMapperInterface $unzerChargeMapper
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface $unzerApiAdapterResponseValidator
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerChargeMapperInterface $unzerChargeMapper,
        UnzerApiAdapterResponseValidatorInterface $unzerApiAdapterResponseValidator
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerChargeMapper = $unzerChargeMapper;
        $this->unzerApiAdapterResponseValidator = $unzerApiAdapterResponseValidator;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiResponseTransfer
     */
    public function chargePayment(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerApiResponseTransfer
    {
        $unzerApiRequestTransfer = $this->prepareChargeRequest($unzerPaymentTransfer);

        return $this->performCharge($unzerApiRequestTransfer, $unzerPaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\UnzerChargeTransfer $unzerChargeTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiChargeResponseTransfer
     */
    public function chargePartialAuthorizablePayment(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        UnzerChargeTransfer $unzerChargeTransfer
    ): UnzerApiChargeResponseTransfer {
        $unzerApiRequestTransfer = $this->prepareAuthorizableChargeRequest($unzerPaymentTransfer, $unzerChargeTransfer);

        $unzerApiResponseTransfer = $this->performAuthorizableCharge($unzerApiRequestTransfer, $unzerPaymentTransfer);
        $this->unzerApiAdapterResponseValidator->assertSuccessResponse($unzerApiResponseTransfer);

        return $unzerApiResponseTransfer->getChargeResponseOrFail();
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
                new UnzerApiChargeRequestTransfer(),
            );

        return (new UnzerApiRequestTransfer())
            ->setChargeRequest($unzerApiChargeRequestTransfer)
            ->setUnzerKeypair($unzerPaymentTransfer->getUnzerKeypairOrFail());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\UnzerChargeTransfer $unzerChargeTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiRequestTransfer
     */
    protected function prepareAuthorizableChargeRequest(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        UnzerChargeTransfer $unzerChargeTransfer
    ): UnzerApiRequestTransfer {
        $unzerApiChargeRequestTransfer = $this
            ->unzerChargeMapper
            ->mapUnzerChargeTransferToUnzerApiChargeRequestTransfer(
                $unzerChargeTransfer,
                new UnzerApiChargeRequestTransfer(),
            );

        return (new UnzerApiRequestTransfer())
            ->setChargeRequest($unzerApiChargeRequestTransfer)
            ->setUnzerKeypair($unzerPaymentTransfer->getUnzerKeypairOrFail());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiRequestTransfer $unzerApiRequestTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiResponseTransfer
     */
    protected function performCharge(UnzerApiRequestTransfer $unzerApiRequestTransfer, UnzerPaymentTransfer $unzerPaymentTransfer): UnzerApiResponseTransfer
    {
        if ($unzerPaymentTransfer->getIsMarketplace()) {
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
        if ($unzerPaymentTransfer->getIsMarketplace()) {
            return $this->unzerApiFacade->performMarketplaceAuthorizableChargeApiCall($unzerApiRequestTransfer);
        }

        return $this->unzerApiFacade->performAuthorizableChargeApiCall($unzerApiRequestTransfer);
    }
}
