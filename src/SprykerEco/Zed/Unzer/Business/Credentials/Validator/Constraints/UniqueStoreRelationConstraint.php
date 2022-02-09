<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints;

use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use Symfony\Component\Validator\Constraint;

class UniqueStoreRelationConstraint extends Constraint
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @param null $options
     * @param array|null $groups
     * @param null $payload
     */
    public function __construct(UnzerReaderInterface $unzerReader, $options = null, array $groups = null, $payload = null)
    {
        parent::__construct($options, $groups, $payload);
        $this->unzerReader = $unzerReader;
    }

    /**
     * @var string
     */
    protected const VALIDATION_MESSAGE = 'Store relation already defined.';

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


