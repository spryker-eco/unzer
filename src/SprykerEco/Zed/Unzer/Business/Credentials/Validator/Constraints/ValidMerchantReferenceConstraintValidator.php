<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidMerchantReferenceConstraintValidator extends ConstraintValidator
{
    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $value
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints\ValidMerchantReferenceConstraint $constraint
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

        if (!$constraint instanceof ValidMerchantReferenceConstraint) {
            throw new UnexpectedTypeException($constraint, ValidMerchantReferenceConstraint::class);
        }

        $merchantCriteriaTransfer = (new MerchantCriteriaTransfer())->setMerchantReference($value->getMerchantReference());

        if ($constraint->getMerchantFacade()->findOne($merchantCriteriaTransfer)) {
            return;
        }

        $this->context
            ->buildViolation($constraint->getMessage())
            ->addViolation();
    }
}
