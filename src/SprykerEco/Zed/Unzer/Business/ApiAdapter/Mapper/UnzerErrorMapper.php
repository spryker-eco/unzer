<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiErrorResponseTransfer;
use Generated\Shared\Transfer\UnzerApiResponseErrorTransfer;
use Generated\Shared\Transfer\UnzerPaymentErrorTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;

class UnzerErrorMapper implements UnzerErrorMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapUnzerApiErrorResponseTransferToUnzerPaymentTransfer(
        UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer {
        foreach ($unzerApiErrorResponseTransfer->getErrors() as $unzerApiResponseErrorTransfer) {
            $unzerPaymentErrorTransfer = $this->createUnzerPaymentErrorTransfer($unzerApiResponseErrorTransfer);

            $unzerPaymentTransfer->addError(createUnzerPaymentErrorTransfer);
        }

        return $unzerPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiResponseErrorTransfer $unzerApiResponseErrorTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentErrorTransfer
     */
    protected function createUnzerPaymentErrorTransfer(UnzerApiResponseErrorTransfer $unzerApiResponseErrorTransfer): UnzerPaymentErrorTransfer
    {
        return (new UnzerPaymentErrorTransfer())
            ->setMessage($unzerApiResponseErrorTransfer->getCustomerMessage())
            ->setErrorCode((int)$unzerApiResponseErrorTransfer->getCode());
    }
}
