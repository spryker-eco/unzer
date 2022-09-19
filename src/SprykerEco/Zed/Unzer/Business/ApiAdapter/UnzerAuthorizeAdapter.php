<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\UnzerApiAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiErrorResponseTransfer;
use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerPaymentErrorTransfer;
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
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function authorizePayment(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        if ($unzerPaymentTransfer->getIsMarketplaceOrFail()) {
            return $this->performMarketplaceAuthorize($unzerPaymentTransfer);
        }

        return $this->performStandardAuthorize($unzerPaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function performMarketplaceAuthorize(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer {
        $unzerApiRequestTransfer = $this->createUnzerApiRequestTransferWithMarketplaceAuthorizeRequest($unzerPaymentTransfer);
        $unzerApiResponseTransfer = $this->unzerApiFacade->performMarketplaceAuthorizeApiCall($unzerApiRequestTransfer);

        if ($unzerApiResponseTransfer->getIsSuccessful()) {
            return $this->unzerAuthorizePaymentMapper
                ->mapUnzerApiMarketplaceAuthorizeResponseTransferToUnzerPaymentTransfer(
                    $unzerApiResponseTransfer->getMarketplaceAuthorizeResponseOrFail(),
                    $unzerPaymentTransfer,
                );
        }

        foreach ($unzerApiResponseTransfer->getErrorResponse()->getErrors() as $unzerApiResponseErrorTransfer) {
            $unzerPaymentTransfer->addError(
                (new UnzerPaymentErrorTransfer())
                    ->setMessage($unzerApiResponseErrorTransfer->getCustomerMessage())
                    ->setErrorCode($unzerApiResponseErrorTransfer->getCode())
            );
        }

        return $unzerPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function performStandardAuthorize(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer {
        $unzerApiRequestTransfer = $this->createUnzerApiRequestTransferWithAuthorizeRequest($unzerPaymentTransfer);
        $unzerApiResponseTransfer = $this->unzerApiFacade->performAuthorizeApiCall($unzerApiRequestTransfer);

        if ($unzerApiResponseTransfer->getIsSuccessful()) {
            return $this->unzerAuthorizePaymentMapper
                ->mapUnzerApiAuthorizeResponseTransferToUnzerPaymentTransfer(
                    $unzerApiResponseTransfer->getAuthorizeResponseOrFail(),
                    $unzerPaymentTransfer,
                );
        }

        foreach ($unzerApiResponseTransfer->getErrorResponse()->getErrors() as $unzerApiResponseErrorTransfer) {
            $unzerPaymentTransfer->addError(
                (new UnzerPaymentErrorTransfer())
                    ->setMessage($unzerApiResponseErrorTransfer->getCustomerMessage())
                    ->setErrorCode($unzerApiResponseErrorTransfer->getCode())
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

    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerApiErrorResponseTransfer|null $unzerApiErrorResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function appendUnzerApiResponseErrorTransfersToCheckoutResponseTransfer(
        CheckoutResponseTransfer $checkoutResponseTransfer,
        ?UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer
    ): CheckoutResponseTransfer {
        if (!$unzerApiErrorResponseTransfer) {
            return $checkoutResponseTransfer;
        }

        foreach ($unzerApiErrorResponseTransfer->getErrors() as $unzerApiResponseErrorTransfer) {
            $checkoutErrorTransfer = $this->createCheckoutErrorTransfer(
                (string)$unzerApiResponseErrorTransfer->getCustomerMessage(),
                (string)$unzerApiResponseErrorTransfer->getCode(),
            );

            $checkoutResponseTransfer->addError($checkoutErrorTransfer);
        }

        return $checkoutResponseTransfer;
    }

    /**
     * @param string $message
     * @param string $errorCode
     *
     * @return \Generated\Shared\Transfer\CheckoutErrorTransfer
     */
    protected function createCheckoutErrorTransfer(string $message, string $errorCode): CheckoutErrorTransfer
    {
        return (new CheckoutErrorTransfer())
            ->setMessage($message)
            ->setErrorCode($errorCode);
    }
}
