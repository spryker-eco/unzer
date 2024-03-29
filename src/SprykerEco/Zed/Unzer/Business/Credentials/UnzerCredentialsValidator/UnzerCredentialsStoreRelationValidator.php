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

class UnzerCredentialsStoreRelationValidator implements UnzerCredentialsValidatorInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE_STORE_RELATION_EMPTY = 'Store relations can not be empty';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_STORE_RELATION_ALREADY_USED = 'Chosen Store relation is already used';

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
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function validate(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer
    {
        $unzerCredentialsResponseTransfer = (new UnzerCredentialsResponseTransfer())->setIsSuccessful(true);
        if (in_array((int)$unzerCredentialsTransfer->getTypeOrFail(), UnzerConstants::UNZER_CREDENTIALS_CHILD_TYPES, true)) {
            return $unzerCredentialsResponseTransfer;
        }

        if (!$this->hasStoreRelations($unzerCredentialsTransfer)) {
            return $unzerCredentialsResponseTransfer->setIsSuccessful(false)
                ->addMessage(
                    (new MessageTransfer())->setMessage(static::ERROR_MESSAGE_STORE_RELATION_EMPTY),
                );
        }

        $unzerCredentialsConditionsTransfer = (new UnzerCredentialsConditionsTransfer())
            ->setStoreIds($unzerCredentialsTransfer->getStoreRelationOrFail()->getIdStores())
            ->setTypes(UnzerConstants::UNZER_CREDENTIALS_MAIN_TYPES);
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions($unzerCredentialsConditionsTransfer);
        $unzerCredentialsCollectionTransfer = $this->unzerReader->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);

        foreach ($unzerCredentialsCollectionTransfer->getUnzerCredentials() as $existingUnzerCredentialsTransfer) {
            if ($existingUnzerCredentialsTransfer->getIdUnzerCredentials() !== (int)$unzerCredentialsTransfer->getIdUnzerCredentials()) {
                $unzerCredentialsResponseTransfer->setIsSuccessful(false)
                    ->addMessage(
                        (new MessageTransfer())->setMessage(static::ERROR_MESSAGE_STORE_RELATION_ALREADY_USED),
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
    protected function hasStoreRelations(UnzerCredentialsTransfer $unzerCredentialsTransfer): bool
    {
        return count($unzerCredentialsTransfer->getStoreRelationOrFail()->getIdStores()) !== 0;
    }
}
