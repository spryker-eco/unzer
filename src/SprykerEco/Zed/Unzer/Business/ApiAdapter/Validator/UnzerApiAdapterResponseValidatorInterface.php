<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;

interface UnzerApiAdapterResponseValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerApiResponseTransfer $unzerApiResponseTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return void
     */
    public function assertSuccessResponse(UnzerApiResponseTransfer $unzerApiResponseTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\UnzerApiResponseTransfer $unzerApiResponseTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function isSuccessfulUnzerApiResponse(
        UnzerApiResponseTransfer $unzerApiResponseTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): CheckoutResponseTransfer;
}
