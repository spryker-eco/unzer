<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\UnzerApiAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerAuthorizePaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerAuthorizeAdapter implements UnzerAuthorizeAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected UnzerToUnzerApiFacadeInterface $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerAuthorizePaymentMapperInterface
     */
    protected UnzerAuthorizePaymentMapperInterface $unzerAuthorizePaymentMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface
     */
    protected UnzerApiAdapterResponseValidatorInterface $unzerApiAdapterResponseValidator;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerAuthorizePaymentMapperInterface $unzerAuthorizePaymentMapper
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface $unzerApiAdapterResponseValidator
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerAuthorizePaymentMapperInterface $unzerAuthorizePaymentMapper,
        UnzerApiAdapterResponseValidatorInterface $unzerApiAdapterResponseValidator
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerAuthorizePaymentMapper = $unzerAuthorizePaymentMapper;
        $this->unzerApiAdapterResponseValidator = $unzerApiAdapterResponseValidator;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function authorizePayment(UnzerPaymentTransfer $unzerPaymentTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): UnzerPaymentTransfer
    {
        if ($unzerPaymentTransfer->getIsMarketplaceOrFail()) {
            return $this->performMarketplaceAuthorize($unzerPaymentTransfer, $checkoutResponseTransfer);
        }

        return $this->performStandardAuthorize($unzerPaymentTransfer, $checkoutResponseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function performMarketplaceAuthorize(UnzerPaymentTransfer $unzerPaymentTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): UnzerPaymentTransfer
    {
        $unzerApiRequestTransfer = $this->createUnzerApiRequestTransferWithMarketplaceAuthorizeRequest($unzerPaymentTransfer);
        $unzerApiResponseTransfer = $this->unzerApiFacade->performMarketplaceAuthorizeApiCall($unzerApiRequestTransfer);

        if ($this->unzerApiAdapterResponseValidator->isSuccessfulUnzerApiResponse($unzerApiResponseTransfer, $checkoutResponseTransfer)) {
            return $this->unzerAuthorizePaymentMapper
                ->mapUnzerApiMarketplaceAuthorizeResponseTransferToUnzerPaymentTransfer(
                    $unzerApiResponseTransfer->getMarketplaceAuthorizeResponseOrFail(),
                    $unzerPaymentTransfer,
                );
        }

        return $unzerPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function performStandardAuthorize(UnzerPaymentTransfer $unzerPaymentTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): UnzerPaymentTransfer
    {
        $unzerApiRequestTransfer = $this->createUnzerApiRequestTransferWithAuthorizeRequest($unzerPaymentTransfer);
        $unzerApiResponseTransfer = $this->unzerApiFacade->performAuthorizeApiCall($unzerApiRequestTransfer);

        if ($this->unzerApiAdapterResponseValidator->isSuccessfulUnzerApiResponse($unzerApiResponseTransfer, $checkoutResponseTransfer)) {
            return $this->unzerAuthorizePaymentMapper
                ->mapUnzerApiAuthorizeResponseTransferToUnzerPaymentTransfer(
                    $unzerApiResponseTransfer->getAuthorizeResponseOrFail(),
                    $unzerPaymentTransfer,
                );
        }

        return $unzerPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiRequestTransfer
     */
    protected function createUnzerApiRequestTransferWithMarketplaceAuthorizeRequest(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerApiRequestTransfer
    {
        $unzerApiMarketplaceAuthorizeRequestTransfer = $this
            ->unzerAuthorizePaymentMapper
            ->mapUnzerPaymentTransferToUnzerApiMarketplaceAuthorizeRequestTransfer(
                $unzerPaymentTransfer,
                new UnzerApiMarketplaceAuthorizeRequestTransfer(),
            );

        return (new UnzerApiRequestTransfer())
            ->setUnzerKeypair($unzerPaymentTransfer->getUnzerKeypairOrFail())
            ->setMarketplaceAuthorizeRequest($unzerApiMarketplaceAuthorizeRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiRequestTransfer
     */
    protected function createUnzerApiRequestTransferWithAuthorizeRequest(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerApiRequestTransfer
    {
        $unzerApiAuthorizeRequestTransfer = $this->unzerAuthorizePaymentMapper
            ->mapUnzerPaymentTransferToUnzerApiAuthorizeRequestTransfer(
                $unzerPaymentTransfer,
                new UnzerApiAuthorizeRequestTransfer(),
            );

        return (new UnzerApiRequestTransfer())
            ->setUnzerKeypair($unzerPaymentTransfer->getUnzerKeypairOrFail())
            ->setAuthorizeRequest($unzerApiAuthorizeRequestTransfer);
    }
}
