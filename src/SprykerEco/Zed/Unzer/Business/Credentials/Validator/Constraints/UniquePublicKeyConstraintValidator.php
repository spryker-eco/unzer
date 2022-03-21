<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints;

use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniquePublicKeyConstraintValidator extends ConstraintValidator
{
    /**
     * @param string $value
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints\UniquePublicKeyConstraint $constraint
     *
     * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
     *
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof UnzerCredentialsTransfer) {
            throw new UnexpectedTypeException($value, UnzerCredentialsTransfer::class);
        }

        if (!$constraint instanceof UniquePublicKeyConstraint) {
            throw new UnexpectedTypeException($constraint, UniquePublicKeyConstraint::class);
        }

        $unzerCredentialsConditionsTransfer = (new UnzerCredentialsConditionsTransfer())
            ->addPublicKey($value->getUnzerKeypairOrFail()->getPublicKey());
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions($unzerCredentialsConditionsTransfer);

        $unzerCredentialsCollectionTransfer = $constraint->getUnzerReader()
            ->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsCollectionTransfer->getUnzerCredentials()->count() === 0) {
            return;
        }

        foreach ($unzerCredentialsCollectionTransfer->getUnzerCredentials() as $unzerCredentialsTransfer) {
            if (
                $unzerCredentialsTransfer->getUnzerKeypairOrFail()->getPublicKey() === $value->getUnzerKeypairOrFail()->getPublicKey() &&
                $unzerCredentialsTransfer->getIdUnzerCredentials() !== (int)$value->getIdUnzerCredentials()
            ) {
                $this->context
                    ->buildViolation($constraint->getMessage())
                    ->addViolation();

                return;
            }
        }
    }
}
