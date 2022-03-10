<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class TransferConstraintValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     *
     * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Validator\Exception\UnexpectedValueException
     *
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof TransferConstraint) {
            throw new UnexpectedTypeException($constraint, TransferConstraint::class);
        }
        if ($value === null) {
            return;
        }
        if (!$value instanceof AbstractTransfer) {
            throw new UnexpectedValueException($value, AbstractTransfer::class);
        }
        $value = $value->toArray(false, true);
        foreach ($constraint->fields as $field => $fieldConstraint) {
            $existsInArray = is_array($value) && array_key_exists($field, $value);

            if (!$existsInArray) {
                $this->context->buildViolation($constraint->getMissingFieldsMessage())
                    ->atPath('[' . $field . ']')
                    ->setParameter('{{ field }}', $this->formatValue($field))
                    ->setInvalidValue(null)
                    ->addViolation();

                continue;
            }
            if ($fieldConstraint) {
                $this->context->getValidator()
                    ->inContext($this->context)
                    ->atPath('[' . $field . ']')
                    ->validate($value[$field], $fieldConstraint);
            }
        }
    }
}
