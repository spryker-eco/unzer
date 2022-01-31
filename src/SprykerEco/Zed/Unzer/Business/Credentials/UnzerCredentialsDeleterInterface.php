<?php

namespace SprykerEco\Zed\Unzer\Business\Credentials;

use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;

interface UnzerCredentialsDeleterInterface
{
    /**
     * @param UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return UnzerCredentialsResponseTransfer
     */
    public function deleteUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer;
}
