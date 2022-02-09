<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidMerchantReferenceConstraintValidator extends ConstraintValidator
{
    /**
     * @param string $value
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints\ValidMerchantReferenceConstraint $constraint
     *
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value) {
            return;
        }

        $merchantCriteriaTransfer = (new MerchantCriteriaTransfer())->setMerchantReference($value);

        if ($constraint->getMerchantFacade()->findOne($merchantCriteriaTransfer)) {
            return;
        }

        $this->context
            ->buildViolation($constraint->getMessage())
            ->addViolation();
    }
}
