<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsValidator;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;

class UnzerCredentialsStoreRelationsValidator implements UnzerCredentialsValidatorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     */
    public function __construct(UnzerReaderInterface $unzerReader)
    {
        $this->unzerReader = $unzerReader;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function validate(UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer): UnzerCredentialsResponseTransfer
    {
        $unzerCredentialsTransfer = $unzerCredentialsResponseTransfer->getUnzerCredentialsOrFail();
        if (!$this->isUnzerCredentialsHaveStoreRelations($unzerCredentialsTransfer)) {
            return $unzerCredentialsResponseTransfer->setIsSuccessful(false)
                ->addMessage(
                    (new MessageTransfer())->setMessage('Store relations can not be empty'),
                );
        }

        $unzerCredentialsConditionsTransfer = (new UnzerCredentialsConditionsTransfer())
            ->setStoreIds($unzerCredentialsTransfer->getStoreRelationOrFail()->getIdStores());
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions($unzerCredentialsConditionsTransfer);
        $unzerCredentialsCollectionTransfer = $this->unzerReader->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);

        foreach ($unzerCredentialsCollectionTransfer->getUnzerCredentials() as $existingUnzerCredentialsTransfer) {
            if (
                $existingUnzerCredentialsTransfer->getType() === (int)$unzerCredentialsTransfer->getType() &&
                $existingUnzerCredentialsTransfer->getIdUnzerCredentials() !== (int)$unzerCredentialsTransfer->getIdUnzerCredentials()
            ) {
                $unzerCredentialsResponseTransfer->setIsSuccessful(false)
                    ->addMessage(
                        (new MessageTransfer())->setMessage('Chosen Store relation is already used'),
                    );

                break;
            }
        }

        return $unzerCredentialsResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return bool
     */
    protected function isUnzerCredentialsHaveStoreRelations(UnzerCredentialsTransfer $unzerCredentialsTransfer): bool
    {
        return !in_array($unzerCredentialsTransfer->getTypeOrFail(), UnzerConstants::UNZER_CHILD_CONFIG_TYPES) &&
            count($unzerCredentialsTransfer->getStoreRelationOrFail()->getIdStores()) !== 0;
    }
}
