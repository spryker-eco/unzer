<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiErrorResponseTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerApiException;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;

abstract class UnzerAbstractApiAdapter
{
    /**
     * @param \Generated\Shared\Transfer\UnzerApiResponseTransfer $unzerApiResponseTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerApiException
     *
     * @return void
     */
    protected function assertSuccessResponse(UnzerApiResponseTransfer $unzerApiResponseTransfer): void
    {
        if ($unzerApiResponseTransfer->getIsSuccessful()) {
            return;
        }

        $unzerApiErrorResponseTransfer = $unzerApiResponseTransfer->getErrorResponseOrFail();

        throw new UnzerApiException($this->concatErrors($unzerApiErrorResponseTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer
     *
     * @return string
     */
    protected function concatErrors(UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer): string
    {
        $errorsMessage = 'Unzer API errors:';
        $unzerApiResponseErrorTransfers = $unzerApiErrorResponseTransfer->getErrors();
        foreach ($unzerApiResponseErrorTransfers as $unzerApiResponseErrorTransfer) {
            $errorsMessage .= $unzerApiResponseErrorTransfer->getCode() . ' ';
        }

        return trim($errorsMessage);
    }
}
