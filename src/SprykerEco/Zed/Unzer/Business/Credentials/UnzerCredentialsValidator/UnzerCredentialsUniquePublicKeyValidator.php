<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;

class UnzerCredentialsUniquePublicKeyValidator implements UnzerCredentialsValidatorInterface
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
     * @param \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function validate(UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer): UnzerCredentialsResponseTransfer
    {
        $unzerCredentialsTransfer = $unzerCredentialsResponseTransfer->getUnzerCredentialsOrFail();
        $unzerCredentialsConditionsTransfer = (new UnzerCredentialsConditionsTransfer())
            ->addPublicKey($unzerCredentialsTransfer->getUnzerKeypairOrFail()->getPublicKeyOrFail());
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions($unzerCredentialsConditionsTransfer);

        $unzerCredentialsCollectionTransfer = $this->unzerReader
            ->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsCollectionTransfer->getUnzerCredentials()->count() === 0) {
            return $unzerCredentialsResponseTransfer;
        }

        foreach ($unzerCredentialsCollectionTransfer->getUnzerCredentials() as $existingUnzerCredentialsTransfer) {
            if (
                $existingUnzerCredentialsTransfer->getUnzerKeypairOrFail()->getPublicKey() === $unzerCredentialsTransfer->getUnzerKeypairOrFail()->getPublicKey() &&
                $existingUnzerCredentialsTransfer->getIdUnzerCredentials() !== (int)$unzerCredentialsTransfer->getIdUnzerCredentials()
            ) {
                $unzerCredentialsResponseTransfer->setIsSuccessful(false)
                    ->addMessage($this->createUniquePublicKeyViolationMessage($unzerCredentialsTransfer));
            }
        }

        return $unzerCredentialsResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\MessageTransfer
     */
    protected function createUniquePublicKeyViolationMessage(UnzerCredentialsTransfer $unzerCredentialsTransfer): MessageTransfer
    {
        $message = sprintf('Provided public key %s already exists!', $unzerCredentialsTransfer->getUnzerKeypairOrFail()->getPublicKeyOrFail());

        return (new MessageTransfer())->setMessage($message);
    }
}
