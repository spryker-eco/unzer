<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints;

use SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface;
use Symfony\Component\Validator\Constraint;

class ValidMerchantReferenceConstraint extends Constraint
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface $merchantFacade
     * @param null $options
     * @param array|null $groups
     * @param null $payload
     */
    public function __construct(UnzerToMerchantFacadeInterface $merchantFacade, $options = null, array $groups = null, $payload = null)
    {
        parent::__construct($options, $groups, $payload);
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * @var string
     */
    public const OPTION_CURRENT_MERCHANT_ID = 'merchantReference';

    /**
     * @var string
     */
    protected const VALIDATION_MESSAGE = 'Unknown merchant reference detected.';

    /**
     * @var string|null
     */
    protected $publicKey;

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
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface
     */
    public function getMerchantFacade(): UnzerToMerchantFacadeInterface
    {
        return $this->merchantFacade;
    }
}

