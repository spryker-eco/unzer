<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
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
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface $unzerEntityManager
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsStoreRelationUpdaterInterface $unzerCredentialsStoreRelationUpdater
     * @param \SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriterInterface $unzerVaultWriter
     */
    public function __construct(
        UnzerEntityManagerInterface $unzerEntityManager,
        UnzerCredentialsStoreRelationUpdaterInterface $unzerCredentialsStoreRelationUpdater,
        UnzerVaultWriterInterface $unzerVaultWriter
    ) {
        $this->unzerEntityManager = $unzerEntityManager;
        $this->unzerCredentialsStoreRelationUpdater = $unzerCredentialsStoreRelationUpdater;
        $this->unzerVaultWriter = $unzerVaultWriter;
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
            $unzerCredentialsTransfer->getKeypairId(),
            $unzerCredentialsTransfer->getUnzerKeypairOrFail()->getPrivateKey(),
        );

        if ($unzerCredentialsTransfer->getStoreRelation() !== null) {
            $storeRelationTransfer = $unzerCredentialsTransfer->getStoreRelation()
                ->setIdEntity($unzerCredentialsTransfer->getIdUnzerCredentialsOrFail());
            $this->unzerCredentialsStoreRelationUpdater->update($storeRelationTransfer);
        }

        return (new UnzerCredentialsResponseTransfer())
            ->setIsSuccessful(true)
            ->setUnzerCredentials($unzerCredentialsTransfer);
    }
}
