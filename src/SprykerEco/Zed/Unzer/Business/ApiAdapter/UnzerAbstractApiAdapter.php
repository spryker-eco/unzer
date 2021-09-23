<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiErrorResponseTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerApiException;

abstract class UnzerAbstractApiAdapter
{
    /**
     * @param \Generated\Shared\Transfer\UnzerApiResponseTransfer $unzerApiResponseTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerApiException
     *
     * @return void
     */
    protected function checkSuccessResponse(UnzerApiResponseTransfer $unzerApiResponseTransfer)
    {
        if ($unzerApiResponseTransfer->getIsSuccess()) {
            return;
        }

        $errorMessage = $unzerApiResponseTransfer->getErrorResponseOrFail();

        throw new UnzerApiException($this->concatErrors($errorMessage));
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiErrorResponseTransfer $apiErrorResponseTransfer
     *
     * @return string
     */
    protected function concatErrors(UnzerApiErrorResponseTransfer $apiErrorResponseTransfer): string
    {
        $errorsMessage = 'Unzer API errors:';
        $errors = $apiErrorResponseTransfer->getErrors();
        foreach ($errors as $error) {
            $errorsMessage .= $error->getCode() . ' ';
        }

        return $errorsMessage;
    }
}
