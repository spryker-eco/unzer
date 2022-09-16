<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator;

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
}
