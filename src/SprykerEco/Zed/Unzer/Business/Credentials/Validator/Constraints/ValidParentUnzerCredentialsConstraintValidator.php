<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints;

use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidParentUnzerCredentialsConstraintValidator extends ConstraintValidator
{
    /**
     * @param string $value
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints\UniquePublicKeyConstraint $constraint
     *
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value) {
            return;
        }

        $unzerCredentialsConditionsTransfer = (new UnzerCredentialsConditionsTransfer())->addId($value);
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions($unzerCredentialsConditionsTransfer);
        $unzerCredentialsTransfer = $constraint->getUnzerReader()->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsTransfer && $unzerCredentialsTransfer->getType() === UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE) {
            return;
        }

        $this->context
            ->buildViolation($constraint->getMessage())
            ->addViolation();
    }
}
