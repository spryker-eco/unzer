<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials;

use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;

interface UnzerCredentialsValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function isUnzerConfigValid(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer;
}
