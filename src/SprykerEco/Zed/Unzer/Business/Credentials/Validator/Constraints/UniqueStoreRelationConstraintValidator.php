<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints;

use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueStoreRelationConstraintValidator extends ConstraintValidator
{
    /**
     * @param string $value
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints\UniqueStoreRelationConstraint $constraint
     *
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value || !is_array($value['idStores'])) {
            return;
        }

        $unzerCredentialsConditionsTransfer = (new UnzerCredentialsConditionsTransfer())
            ->setStoreIds($value['idStores'])
            ->setTypes();
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions($unzerCredentialsConditionsTransfer);
        $unzerCredentialsCollectionTransfer = $constraint->getUnzerReader()->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);

        foreach ($unzerCredentialsCollectionTransfer->getUnzerCredentials() as $unzerCredentialsTransfer) {
            if (in_array($unzerCredentialsTransfer->getType(), [UnzerConstants::UNZER_CONFIG_TYPE_STANDARD, UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE])) {
                $this->context
                    ->buildViolation($constraint->getMessage())
                    ->addViolation();
            }
        }
    }
}
