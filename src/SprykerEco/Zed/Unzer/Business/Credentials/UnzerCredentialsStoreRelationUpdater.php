<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials;

use Generated\Shared\Transfer\StoreRelationTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;

class UnzerCredentialsStoreRelationUpdater implements UnzerCredentialsStoreRelationUpdaterInterface
{
    use TransactionTrait;

    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface
     */
    protected $unzerEntityManager;

    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface $unzerEntityManager
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface $unzerRepository
     */
    public function __construct(
        UnzerEntityManagerInterface $unzerEntityManager,
        UnzerRepositoryInterface $unzerRepository
    ) {
        $this->unzerEntityManager = $unzerEntityManager;
        $this->unzerRepository = $unzerRepository;
    }

    /**
     * @param \Generated\Shared\Transfer\StoreRelationTransfer $storeRelationTransfer
     *
     * @return void
     */
    public function update(StoreRelationTransfer $storeRelationTransfer): void
    {
        $this->getTransactionHandler()->handleTransaction(function () use ($storeRelationTransfer): void {
            $this->executeUpdateStoreRelationTransaction($storeRelationTransfer);
        });
    }

    /**
     * @param \Generated\Shared\Transfer\StoreRelationTransfer $storeRelationTransfer
     *
     * @return void
     */
    protected function executeUpdateStoreRelationTransaction(StoreRelationTransfer $storeRelationTransfer): void
    {
        $storeRelationTransfer->requireIdEntity();

        $currentIdStores = $this->getIdStoresByIdUnzerCredentials($storeRelationTransfer->getIdEntity());
        $requestedIdStores = $storeRelationTransfer->getIdStores() ?? [];

        $saveIdStores = array_diff($requestedIdStores, $currentIdStores);
        $deleteIdStores = array_diff($currentIdStores, $requestedIdStores);

        $this->unzerEntityManager->createUnzerCredentialsStoreRelationsForStores($saveIdStores, $storeRelationTransfer->getIdEntity());
        $this->unzerEntityManager->deleteUnzerCredentialsStoreRelationsForStores($deleteIdStores, $storeRelationTransfer->getIdEntity());
    }

    /**
     * @param int $idUnzerCredentials
     *
     * @return array<int>
     */
    protected function getIdStoresByIdUnzerCredentials(int $idUnzerCredentials): array
    {
        $storeRelation = $this->unzerRepository->getStoreRelationByIdUnzerCredentials($idUnzerCredentials);

        return $storeRelation->getIdStores();
    }
}
