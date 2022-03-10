<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints\UnzerCredentialsConstraintsProviderInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToValidationAdapterInterface;

class UnzerCredentialsConstraintsValidator implements UnzerCredentialsConstraintsValidatorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToValidationAdapterInterface
     */
    protected UnzerToValidationAdapterInterface $unzerCredentialsValidationAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints\UnzerCredentialsConstraintsProviderInterface
     */
    protected UnzerCredentialsConstraintsProviderInterface $unzerCredentialsConstraintsProvider;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToValidationAdapterInterface $unzerCredentialsValidationAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints\UnzerCredentialsConstraintsProviderInterface $unzerCredentialsConstraintsProvider
     */
    public function __construct(
        UnzerToValidationAdapterInterface $unzerCredentialsValidationAdapter,
        UnzerCredentialsConstraintsProviderInterface $unzerCredentialsConstraintsProvider
    ) {
        $this->unzerCredentialsValidationAdapter = $unzerCredentialsValidationAdapter;
        $this->unzerCredentialsConstraintsProvider = $unzerCredentialsConstraintsProvider;
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
                $unzerCredentialsTransfer,
                $this->unzerCredentialsConstraintsProvider->getConstraintsCollectionByConfigType($unzerCredentialsTransfer->getTypeOrFail()),
            );

        $unzerCredentialsResponseTransfer = (new UnzerCredentialsResponseTransfer())
            ->setIsSuccessful($constraintViolationList->count() === 0);

        foreach ($constraintViolationList as $constraintViolation) {
            /** @var \Symfony\Component\Validator\ConstraintViolationInterface $constraintViolation */
            $unzerCredentialsResponseTransfer->addMessage((new MessageTransfer())
                ->setValue($constraintViolation->getPropertyPath())
                ->setMessage($constraintViolation->getMessage()));
        }

        return $unzerCredentialsResponseTransfer;
    }
}
