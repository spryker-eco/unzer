<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints;

use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use Symfony\Component\Validator\Constraint;

class UniquePublicKeyConstraint extends Constraint
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param mixed $options
     * @param array<int, string>|null $groups
     * @param mixed $payload
     */
    public function __construct(UnzerReaderInterface $unzerReader, $options = null, ?array $groups = null, $payload = null)
    {
        parent::__construct($options, $groups, $payload);
        $this->unzerReader = $unzerReader;
    }

    /**
     * @var string
     */
    protected const VALIDATION_MESSAGE = 'Unzer public key is already used.';

    /**
     * @return string
     */
    public function getTargets(): string
    {
        return static::CLASS_CONSTRAINT;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return static::VALIDATION_MESSAGE;
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    public function getUnzerReader(): UnzerReaderInterface
    {
        return $this->unzerReader;
    }
}
