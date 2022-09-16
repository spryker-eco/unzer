<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator;

use Generated\Shared\Transfer\UnzerApiErrorResponseTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;

class UnzerApiAdapterResponseValidator implements UnzerApiAdapterResponseValidatorInterface
{
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
}
