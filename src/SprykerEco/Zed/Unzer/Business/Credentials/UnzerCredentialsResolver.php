<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials;

use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;

class UnzerCredentialsResolver implements UnzerCredentialsResolverInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     */
    public function __construct(UnzerReaderInterface $unzerReader)
    {
        $this->unzerReader = $unzerReader;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function resolveUnzerCredentialsByCriteriaTransfer(UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer): UnzerCredentialsTransfer
    {
        $unzerCredentialsTransfer = $this->unzerReader->getUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsTransfer === null) {
            throw new UnzerException(
                sprintf(
                    'UnzerCredentials not found for next criteria: %s',
                    $unzerCredentialsCriteriaTransfer->serialize(),
                ),
            );
        }

        return $unzerCredentialsTransfer;
    }
}
