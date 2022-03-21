<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Dependency;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UnzerToValidationAdapter implements UnzerToValidationAdapterInterface
{
    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function createValidator(): ValidatorInterface
    {
        return Validation::createValidator();
    }
}
