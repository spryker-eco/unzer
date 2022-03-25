<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriterInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface;

class UnzerCredentialsUpdater implements UnzerCredentialsUpdaterInterface
{
    use TransactionTrait;

    /**
     * @var string
     */
    protected const MESSAGE_UPDATE_ERROR = 'It is impossible to update this Unzer config';

    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface
     */
    protected $unzerEntityManager;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsStoreRelationUpdaterInterface
     */
    protected $unzerCredentialsStoreRelationUpdater;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriterInterface
     */
    protected $unzerVaultWriter;

    /**
     * @var UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface $unzerEntityManager
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsStoreRelationUpdaterInterface $unzerCredentialsStoreRelationUpdater
     * @param \SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriterInterface $unzerVaultWriter
     * @param UnzerReaderInterface $unzerReader
     */
    public function __construct(
        UnzerEntityManagerInterface $unzerEntityManager,
        UnzerCredentialsStoreRelationUpdaterInterface $unzerCredentialsStoreRelationUpdater,
        UnzerVaultWriterInterface $unzerVaultWriter,
        UnzerReaderInterface $unzerReader
    ) {
        $this->unzerEntityManager = $unzerEntityManager;
        $this->unzerCredentialsStoreRelationUpdater = $unzerCredentialsStoreRelationUpdater;
        $this->unzerVaultWriter = $unzerVaultWriter;
        $this->unzerReader = $unzerReader;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function updateUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer
    {
        return $this->getTransactionHandler()->handleTransaction(function () use ($unzerCredentialsTransfer) {
            return $this->executeUpdateUnzerCredentialsTransaction($unzerCredentialsTransfer);
        });
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    protected function executeUpdateUnzerCredentialsTransaction(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer
    {
        if ($unzerCredentialsTransfer->getStoreRelation() !== null) {
            $storeRelationTransfer = $unzerCredentialsTransfer->getStoreRelationOrFail()
                ->setIdEntity($unzerCredentialsTransfer->getIdUnzerCredentialsOrFail());
            $this->unzerCredentialsStoreRelationUpdater->update($storeRelationTransfer);
        }

        if ($unzerCredentialsTransfer->getTypeOrFail() === UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE) {
            $this->updateChildUnzerCredentialsStoreRelations($unzerCredentialsTransfer);
        }

        $unzerCredentialsTransfer = $this->unzerEntityManager->updateUnzerCredentials($unzerCredentialsTransfer);
        if ($unzerCredentialsTransfer === null) {
            return (new UnzerCredentialsResponseTransfer())
                ->setIsSuccessful(false)
                ->addMessage(
                    (new MessageTransfer())
                    ->setValue(static::MESSAGE_UPDATE_ERROR),
                );
        }

        $this->unzerVaultWriter->storeUnzerPrivateKey(
            $unzerCredentialsTransfer->getKeypairIdOrFail(),
            $unzerCredentialsTransfer->getUnzerKeypairOrFail()->getPrivateKeyOrFail(),
        );

        return (new UnzerCredentialsResponseTransfer())
            ->setIsSuccessful(true)
            ->setUnzerCredentials($unzerCredentialsTransfer);
    }

    /**
     * @param UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return void
     */
    protected function updateChildUnzerCredentialsStoreRelations(UnzerCredentialsTransfer $unzerCredentialsTransfer): void
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions(
            (new UnzerCredentialsConditionsTransfer())->addParentId($unzerCredentialsTransfer->getIdUnzerCredentialsOrFail())
        );

        $unzerCredentialsCollectionTransfer = $this->unzerReader->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);
        foreach ($unzerCredentialsCollectionTransfer->getUnzerCredentials() as $childUnzerCredentialsTransfer) {
            $storeRelationTransfer = $unzerCredentialsTransfer->getStoreRelationOrFail()
                ->setIdEntity($childUnzerCredentialsTransfer->getIdUnzerCredentialsOrFail());
            $this->unzerCredentialsStoreRelationUpdater->update($storeRelationTransfer);
        }
    }
}
