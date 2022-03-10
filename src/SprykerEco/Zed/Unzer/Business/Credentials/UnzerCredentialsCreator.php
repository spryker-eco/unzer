<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Propel\Runtime\Propel;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerApiException;
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
        $propelConnection = Propel::getConnection();
        $unzerCredentialsResponseTransfer = new UnzerCredentialsResponseTransfer();

        try {
            $propelConnection->beginTransaction();
            $unzerCredentialsResponseTransfer = $this->executeCreateUnzerCredentials($unzerCredentialsTransfer);
            $propelConnection->commit();
        } catch (UnzerApiException $unzerApiException) {
            $propelConnection->rollBack();

            return $unzerCredentialsResponseTransfer
                ->setIsSuccessful(false)
                ->addMessage(
                    (new MessageTransfer())->setMessage($unzerApiException->getMessage()),
                );
        }

        return $unzerCredentialsResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    protected function executeCreateUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer
    {
        $unzerCredentialsTransfer = $this->unzerEntityManager
            ->createUnzerCredentials(
                $unzerCredentialsTransfer->setKeypairId(
                    $this->utilTextService->generateUniqueId('', true),
                ),
            );
        $this->unzerVaultWriter->storeUnzerPrivateKey(
            $unzerCredentialsTransfer->getKeypairId(),
            $unzerCredentialsTransfer->getUnzerKeypairOrFail()->getPrivateKey(),
        );

        $unzerCredentialsTransfer = $this->createStoreRelationUnzerCredentials($unzerCredentialsTransfer);
        $unzerCredentialsTransfer = $this->createChildUnzerCredentials($unzerCredentialsTransfer);
        $this->unzerNotificationConfigurator->setNotificationUrl($unzerCredentialsTransfer);

        return (new UnzerCredentialsResponseTransfer())
            ->setIsSuccessful(true)
            ->setUnzerCredentials($unzerCredentialsTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    protected function createStoreRelationUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsTransfer
    {
        if (!$unzerCredentialsTransfer->getStoreRelation()) {
            return $unzerCredentialsTransfer;
        }

        $storeRelationTransfer = $unzerCredentialsTransfer->getStoreRelationOrFail()
            ->setIdEntity($unzerCredentialsTransfer->getIdUnzerCredentialsOrFail());
        $this->unzerCredentialsStoreRelationUpdater->update($storeRelationTransfer);
        $unzerCredentialsTransfer->setStoreRelation($storeRelationTransfer);

        return $unzerCredentialsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    protected function createChildUnzerCredentials(
        UnzerCredentialsTransfer $unzerCredentialsTransfer
    ): UnzerCredentialsTransfer {
        if (!$unzerCredentialsTransfer->getChildUnzerCredentials()) {
            return $unzerCredentialsTransfer;
        }

        return $this->createMainMerchantUnzerCredentials($unzerCredentialsTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    protected function createMainMerchantUnzerCredentials(
        UnzerCredentialsTransfer $unzerCredentialsTransfer
    ): UnzerCredentialsTransfer {
        $childUnzerCredentialsTransfer = $unzerCredentialsTransfer->getChildUnzerCredentialsOrFail()
            ->setParentIdUnzerCredentials($unzerCredentialsTransfer->getIdUnzerCredentials())
            ->setType(UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT);

        $childUnzerCredentialsResponseTransfer = $this->executeCreateUnzerCredentials(
            $childUnzerCredentialsTransfer,
        );

        return $unzerCredentialsTransfer->setChildUnzerCredentials(
            $childUnzerCredentialsResponseTransfer->getUnzerCredentialsOrFail(),
        );
    }
}
