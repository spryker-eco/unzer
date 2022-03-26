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
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;

class UnzerCredentialsParentCredentialsValidator implements UnzerCredentialsValidatorInterface
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
        if (!in_array($unzerCredentialsTransfer->getTypeOrFail(), UnzerConstants::UNZER_CHILD_CONFIG_TYPES, true)) {
            return $unzerCredentialsResponseTransfer;
        }

        $unzerCredentialsConditionsTransfer = (new UnzerCredentialsConditionsTransfer())->addId($unzerCredentialsTransfer->getParentIdUnzerCredentialsOrFail());
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions($unzerCredentialsConditionsTransfer);
        $unzerCredentialsTransfer = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsTransfer && $unzerCredentialsTransfer->getType() === UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE) {
            return $unzerCredentialsResponseTransfer;
        }

        return $unzerCredentialsResponseTransfer->setIsSuccessful(false)
            ->addMessage($this->createParentCredentialsNotFoundViolationMessage($unzerCredentialsTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\MessageTransfer
     */
    protected function createParentCredentialsNotFoundViolationMessage(UnzerCredentialsTransfer $unzerCredentialsTransfer): MessageTransfer
    {
        $message = sprintf('Parent Unzer credentials with id %s not found!', $unzerCredentialsTransfer->getParentIdUnzerCredentialsOrFail());

        return (new MessageTransfer())->setMessage($message);
    }
}
