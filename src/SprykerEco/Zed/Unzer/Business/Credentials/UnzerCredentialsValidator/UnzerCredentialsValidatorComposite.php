<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator;

use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;

class UnzerCredentialsValidatorComposite implements UnzerCredentialsValidatorInterface
{
    /**
     * @var array<\SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsValidatorInterface>
     */
    protected $unzerCredentialsValidators;

    /**
     * @param array<\SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator\UnzerCredentialsValidatorInterface> $unzerCredentialsValidators
     */
    public function __construct(array $unzerCredentialsValidators)
    {
        $this->unzerCredentialsValidators = $unzerCredentialsValidators;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function validate(UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer): UnzerCredentialsResponseTransfer
    {
        foreach ($this->unzerCredentialsValidators as $unzerCredentialsValidator) {
            $unzerCredentialsResponseTransfer = $unzerCredentialsValidator->validate($unzerCredentialsResponseTransfer);
        }

        return $unzerCredentialsResponseTransfer;
    }
}
