<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator;

use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;

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
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function validate(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer
    {
        $unzerCredentialsResponseTransfer = (new UnzerCredentialsResponseTransfer())->setIsSuccessful(true);

        foreach ($this->unzerCredentialsValidators as $unzerCredentialsValidator) {
            $unzerCredentialsIterationResponseTransfer = $unzerCredentialsValidator->validate($unzerCredentialsTransfer);
            $unzerCredentialsResponseTransfer = $this->mergeIterationUnzerCredentialsResponse(
                $unzerCredentialsResponseTransfer,
                $unzerCredentialsIterationResponseTransfer,
            );
        }

        return $unzerCredentialsResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer $unzerCredentialsIterationResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    protected function mergeIterationUnzerCredentialsResponse(
        UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer,
        UnzerCredentialsResponseTransfer $unzerCredentialsIterationResponseTransfer
    ): UnzerCredentialsResponseTransfer {
        $unzerCredentialsResponseTransfer->setIsSuccessful(
            $unzerCredentialsResponseTransfer->getIsSuccessful()
            && $unzerCredentialsIterationResponseTransfer->getIsSuccessful(),
        );

        foreach ($unzerCredentialsIterationResponseTransfer->getMessages() as $messageTransfer) {
            $unzerCredentialsResponseTransfer->addMessage($messageTransfer);
        }

        return $unzerCredentialsResponseTransfer;
    }
}
