<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials;

use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\Notification\Configurator\UnzerNotificationConfiguratorInterface;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriterInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUtilTextServiceInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface;

class UnzerCredentialsCreator implements UnzerCredentialsCreatorInterface
{
    use TransactionTrait;

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
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUtilTextServiceInterface
     */
    protected $utilTextService;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Notification\Configurator\UnzerNotificationConfiguratorInterface
     */
    protected $unzerNotificationConfigurator;

    /**
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface $unzerEntityManager
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsStoreRelationUpdaterInterface $unzerCredentialsStoreRelationUpdater
     * @param \SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriterInterface $unzerVaultWriter
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUtilTextServiceInterface $utilTextService
     * @param \SprykerEco\Zed\Unzer\Business\Notification\Configurator\UnzerNotificationConfiguratorInterface $unzerNotificationConfigurator
     */
    public function __construct(
        UnzerEntityManagerInterface $unzerEntityManager,
        UnzerCredentialsStoreRelationUpdaterInterface $unzerCredentialsStoreRelationUpdater,
        UnzerVaultWriterInterface $unzerVaultWriter,
        UnzerToUtilTextServiceInterface $utilTextService,
        UnzerNotificationConfiguratorInterface $unzerNotificationConfigurator
    ) {
        $this->unzerEntityManager = $unzerEntityManager;
        $this->unzerCredentialsStoreRelationUpdater = $unzerCredentialsStoreRelationUpdater;
        $this->unzerVaultWriter = $unzerVaultWriter;
        $this->utilTextService = $utilTextService;
        $this->unzerNotificationConfigurator = $unzerNotificationConfigurator;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function createUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer
    {
        return $this->getTransactionHandler()->handleTransaction(function () use ($unzerCredentialsTransfer) {
            return $this->executeCreateUnzerCredentialsTransaction($unzerCredentialsTransfer);
        });
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function createUnzerCredentialsAndSetUnzerNotificationUrl(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer
    {
        return $this->getTransactionHandler()->handleTransaction(function () use ($unzerCredentialsTransfer) {
            $unzerCredentialsResponseTransfer = $this->executeCreateUnzerCredentialsTransaction($unzerCredentialsTransfer);
            $unzerCredentialsResponseTransfer = $this->executeCreateChildUnzerCredentialsTransaction($unzerCredentialsResponseTransfer);
            $this->unzerNotificationConfigurator->setNotificationUrl($unzerCredentialsResponseTransfer->getUnzerCredentials());

            return $unzerCredentialsResponseTransfer;
        });
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    protected function executeCreateUnzerCredentialsTransaction(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer
    {
        $uniqueId = $this->utilTextService->generateUniqueId('', true);
        $unzerCredentialsTransfer->setKeypairId($uniqueId);

        $unzerCredentialsTransfer = $this->unzerEntityManager->createUnzerCredentials($unzerCredentialsTransfer);
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

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    protected function executeCreateChildUnzerCredentialsTransaction(
        UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer
    ): UnzerCredentialsResponseTransfer {
        $unzerCredentialsTransfer = $unzerCredentialsResponseTransfer->getUnzerCredentialsOrFail();
        if ($unzerCredentialsTransfer->getChildUnzerCredentials() === null) {
            return $unzerCredentialsResponseTransfer;
        }

        $unzerCredentialsTransfer = $this->createMainMerchantUnzerCredentials($unzerCredentialsResponseTransfer->getUnzerCredentials());
        $unzerCredentialsResponseTransfer->setUnzerCredentials($unzerCredentialsTransfer);

        $this->unzerNotificationConfigurator->setNotificationUrl($unzerCredentialsTransfer->getChildUnzerCredentials());

        return $unzerCredentialsResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    protected function createMainMerchantUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsTransfer
    {
        $childUnzerCredentialsTransfer = $unzerCredentialsTransfer->getChildUnzerCredentialsOrFail()
            ->setParentIdUnzerCredentials($unzerCredentialsTransfer->getIdUnzerCredentials())
            ->setType(UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT);

        $childUnzerCredentialsResponseTransfer = $this->executeCreateUnzerCredentialsTransaction(
            $childUnzerCredentialsTransfer,
        );

        return $unzerCredentialsTransfer->setChildUnzerCredentials(
            $childUnzerCredentialsResponseTransfer->getUnzerCredentialsOrFail(),
        );
    }
}
