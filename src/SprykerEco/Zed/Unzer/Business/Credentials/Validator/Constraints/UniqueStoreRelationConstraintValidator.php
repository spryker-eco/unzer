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

class UniqueStoreRelationConstraintValidator extends ConstraintValidator
{
    /**
     * @param array $value
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints\UniqueStoreRelationConstraint $constraint
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

        if (!$constraint instanceof UniqueStoreRelationConstraint) {
            throw new UnexpectedTypeException($constraint, UniqueStoreRelationConstraint::class);
        }

        $unzerCredentialsConditionsTransfer = (new UnzerCredentialsConditionsTransfer())
            ->setStoreIds($value->getStoreRelationOrFail()->getIdStores());
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions($unzerCredentialsConditionsTransfer);
        $unzerCredentialsCollectionTransfer = $constraint->getUnzerReader()->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);

        foreach ($unzerCredentialsCollectionTransfer->getUnzerCredentials() as $unzerCredentialsTransfer) {
            if ($unzerCredentialsTransfer->getType() === (int)$value->getType() && $unzerCredentialsTransfer->getIdUnzerCredentials() !== (int)$value->getIdUnzerCredentials()) {
                $this->context
                    ->buildViolation($constraint->getMessage())
                    ->addViolation();

                return;
            }
        }
    }
}
