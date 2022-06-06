<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials;

use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;

interface UnzerCredentialsResolverInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function resolveUnzerCredentialsByCriteriaTransfer(UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer): UnzerCredentialsTransfer;
}
