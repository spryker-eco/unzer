<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface;

class UnzerCredentialsMerchantReferenceValidator implements UnzerCredentialsValidatorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface $merchantFacade
     */
    public function __construct(UnzerToMerchantFacadeInterface $merchantFacade)
    {
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function validate(UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer): UnzerCredentialsResponseTransfer
    {
        $unzerCredentialsTransfer = $unzerCredentialsResponseTransfer->getUnzerCredentialsOrFail();
        if (!in_array($unzerCredentialsTransfer->getTypeOrFail(), UnzerConstants::UNZER_CHILD_CONFIG_TYPES, true)) {
            return $unzerCredentialsResponseTransfer;
        }

        $merchantCriteriaTransfer = (new MerchantCriteriaTransfer())->setMerchantReference(
            $unzerCredentialsTransfer->getMerchantReferenceOrFail(),
        );

        if ($this->merchantFacade->findOne($merchantCriteriaTransfer)) {
            return $unzerCredentialsResponseTransfer;
        }

        return $unzerCredentialsResponseTransfer->setIsSuccessful(false)
            ->addMessage($this->createMerchantNotExistsViolationMessage($unzerCredentialsTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\MessageTransfer
     */
    protected function createMerchantNotExistsViolationMessage(UnzerCredentialsTransfer $unzerCredentialsTransfer): MessageTransfer
    {
        $message = sprintf('Merchant with reference %s does not exist!', $unzerCredentialsTransfer->getMerchantReferenceOrFail());

        return (new MessageTransfer())->setMessage($message);
    }
}
