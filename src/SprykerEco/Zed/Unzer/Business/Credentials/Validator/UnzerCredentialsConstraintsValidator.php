<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\UnzerCredentialsParameterMessageTransfer;
use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Lcobucci\JWT\Validation\ConstraintViolation;
use SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints\UnzerCredentialsConstraintsProviderInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToValidationAdapterInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

class UnzerCredentialsConstraintsValidator implements UnzerCredentialsConstraintsValidatorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToValidationAdapterInterface
     */
    protected UnzerToValidationAdapterInterface $unzerCredentialsValidationAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints\UnzerCredentialsConstraintsProviderInterface
     */
    protected UnzerCredentialsConstraintsProviderInterface $unzerCredentailsContraintsProvider;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToValidationAdapterInterface $unzerCredentialsValidationAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints\UnzerCredentialsConstraintsProviderInterface $unzerCredentailsContraintsProvider
     */
    public function __construct(
        UnzerToValidationAdapterInterface $unzerCredentialsValidationAdapter,
        UnzerCredentialsConstraintsProviderInterface $unzerCredentailsContraintsProvider
    ) {
        $this->unzerCredentialsValidationAdapter = $unzerCredentialsValidationAdapter;
        $this->unzerCredentailsContraintsProvider = $unzerCredentailsContraintsProvider;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function validate(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer
    {
        $constraintViolationList = $this->unzerCredentialsValidationAdapter
            ->createValidator()
            ->validate(
                $unzerCredentialsTransfer->toArrayRecursiveCamelCased(),
                $this->unzerCredentailsContraintsProvider->getConstraintsCollectionByConfigType($unzerCredentialsTransfer->getTypeOrFail()),
            );

        $unzerCredentialsResponseTransfer = (new UnzerCredentialsResponseTransfer())
            ->setIsSuccessful($constraintViolationList->count() === 0);

        foreach ($constraintViolationList as $constraintViolation) {
            /** @var ConstraintViolationInterface $constraintViolation */
            $unzerCredentialsResponseTransfer->addMessage((new MessageTransfer())
                ->setValue($constraintViolation->getPropertyPath())
                ->setMessage($constraintViolation->getMessage())
            );
        }

        return $unzerCredentialsResponseTransfer;
    }
}
