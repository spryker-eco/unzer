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

class UnzerCredentialsUniqueMerchantReferenceValidator implements UnzerCredentialsValidatorInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE_MERCHANT_REFERENCE_ALREADY_EXIST = 'Provided merchant reference already exists!';

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
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function validate(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer
    {
        $unzerCredentialsResponseTransfer = (new UnzerCredentialsResponseTransfer())->setIsSuccessful(true);
        if (
            !in_array((int)$unzerCredentialsTransfer->getTypeOrFail(), UnzerConstants::UNZER_CHILD_CONFIG_TYPES, true)
            || $unzerCredentialsTransfer->getMerchantReference() === null
        ) {
            return $unzerCredentialsResponseTransfer;
        }

        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addParentId($unzerCredentialsTransfer->getParentIdUnzerCredentialsOrFail()),
            );

        $unzerCredentialsCollectionTransfer = $this->unzerReader
            ->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);

        foreach ($unzerCredentialsCollectionTransfer->getUnzerCredentials() as $storedUnzerCredentials) {
            if (
                $storedUnzerCredentials->getMerchantReference() === $unzerCredentialsTransfer->getMerchantReference()
                && $storedUnzerCredentials->getIdUnzerCredentialsOrFail() !== $unzerCredentialsTransfer->getIdUnzerCredentials()
            ) {
                return $unzerCredentialsResponseTransfer->setIsSuccessful(false)
                    ->addMessage($this->createMerchantReferenceAlreadyUsedViolationMessage($unzerCredentialsTransfer));
            }
        }

        return $unzerCredentialsResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\MessageTransfer
     */
    protected function createMerchantReferenceAlreadyUsedViolationMessage(UnzerCredentialsTransfer $unzerCredentialsTransfer): MessageTransfer
    {
        return (new MessageTransfer())
            ->setMessage(static::ERROR_MESSAGE_MERCHANT_REFERENCE_ALREADY_EXIST)
            ->setParameters([
                UnzerCredentialsTransfer::MERCHANT_REFERENCE => $unzerCredentialsTransfer->getMerchantReferenceOrFail(),
            ]);
    }
}
