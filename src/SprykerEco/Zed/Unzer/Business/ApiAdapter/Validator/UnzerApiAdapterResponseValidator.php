<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator;

use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\UnzerApiErrorResponseTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;

class UnzerApiAdapterResponseValidator implements UnzerApiAdapterResponseValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerApiResponseTransfer $unzerApiResponseTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function isSuccessfulUnzerApiResponse(
        UnzerApiResponseTransfer $unzerApiResponseTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): CheckoutResponseTransfer {
        if ($unzerApiResponseTransfer->getIsSuccessful()) {
            return true;
        }

        $checkoutResponseTransfer = $checkoutResponseTransfer->setIsSuccess(false);
        $checkoutResponseTransfer = $this->appendUnzerApiResponseErrorTransfersToCheckoutResponseTransfer(
            $checkoutResponseTransfer,
            $unzerApiResponseTransfer->getErrorResponse(),
        );

        return false;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiResponseTransfer $unzerApiResponseTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return void
     */
    public function assertSuccessResponse(UnzerApiResponseTransfer $unzerApiResponseTransfer): void
    {
        if ($unzerApiResponseTransfer->getIsSuccessful()) {
            return;
        }

        $unzerApiErrorResponseTransfer = $unzerApiResponseTransfer->getErrorResponseOrFail();

        throw new UnzerException($this->concatErrors($unzerApiErrorResponseTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer
     *
     * @return string
     */
    protected function concatErrors(UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer): string
    {
        $errorsMessage = 'Unzer API errors: ';
        $unzerApiResponseErrorTransfers = $unzerApiErrorResponseTransfer->getErrors();
        foreach ($unzerApiResponseErrorTransfers as $unzerApiResponseErrorTransfer) {
            $errorsMessage .= $unzerApiResponseErrorTransfer->getMerchantMessage() . ' ';
        }

        return trim($errorsMessage);
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Processor\UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function appendUnzerApiResponseErrorTransfersToCheckoutResponseTransfer(
        CheckoutResponseTransfer $checkoutResponseTransfer,
        UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer
    ): CheckoutResponseTransfer {
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
