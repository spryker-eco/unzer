<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Persistence;

use Generated\Shared\Transfer\MerchantUnzerParticipantConditionsTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipantQuery;
use Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsQuery;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerPersistenceFactory getFactory()
 */
class UnzerRepository extends AbstractRepository implements UnzerRepositoryInterface
{
    /**
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer|null
     */
    public function findPaymentUnzerByOrderReference(string $orderReference): ?PaymentUnzerTransfer
    {
        $paymentUnzerEntity = $this->getFactory()->createPaymentUnzerQuery()
            ->filterByOrderId($orderReference)
            ->findOne();

        if ($paymentUnzerEntity === null) {
            return null;
        }

        return $this->getFactory()->createUnzerPersistenceMapper()->mapPaymentUnzerEntityToPaymentUnzerTransfer(
            $paymentUnzerEntity,
            new PaymentUnzerTransfer(),
        );
    }

    /**
     * @param string $orderId
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer
     */
    public function getPaymentUnzerOrderItemCollectionByOrderId(string $orderId): PaymentUnzerOrderItemCollectionTransfer
    {
        $paymentUnzerOrderItemEntities = $this->getFactory()
            ->createPaymentUnzerOrderItemQuery()
            ->usePaymentUnzerQuery()
            ->filterByOrderId($orderId)
            ->endUse()
            ->find();

        return $this->getFactory()->createUnzerPersistenceMapper()
            ->mapPaymentUnzerOrderItemEntitiesToPaymentUnzerOrderItemCollectionTransfer(
                $paymentUnzerOrderItemEntities,
                new PaymentUnzerOrderItemCollectionTransfer(),
            );
    }

    /**
     * @param string $paymentId
     * @param string $unzerKeypairId
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer|null
     */
    public function findPaymentUnzerByPaymentIdAndKeypairId(string $paymentId, string $unzerKeypairId): ?PaymentUnzerTransfer
    {
        $paymentUnzerEntity = $this->getFactory()->createPaymentUnzerQuery()
            ->filterByPaymentId($paymentId)
            ->filterByUnzerKeypairId($unzerKeypairId)
            ->findOne();

        if ($paymentUnzerEntity === null) {
            return null;
        }

        return $this->getFactory()->createUnzerPersistenceMapper()
            ->mapPaymentUnzerEntityToPaymentUnzerTransfer($paymentUnzerEntity, new PaymentUnzerTransfer());
    }

    /**
     * @param int $idSalesOrderItem
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer|null
     */
    public function findPaymentUnzerOrderItemByIdSalesOrderItem(int $idSalesOrderItem): ?PaymentUnzerOrderItemTransfer
    {
        $paymentUnzerOrderItemEntity = $this->getFactory()
            ->createPaymentUnzerOrderItemQuery()
            ->filterByFkSalesOrderItem($idSalesOrderItem)
            ->findOne();

        if ($paymentUnzerOrderItemEntity === null) {
            return null;
        }

        return $this->getFactory()->createUnzerPersistenceMapper()
            ->mapPaymentUnzerOrderItemEntityToPaymentUnzerOrderItemTransfer(
                $paymentUnzerOrderItemEntity,
                new PaymentUnzerOrderItemTransfer(),
            );
    }

    /**
     * @param string $paymentId
     * @param string $transactionType
     * @param string|null $participantId
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer|null
     */
    public function findPaymentUnzerTransactionByPaymentIdAndParticipantId(
        string $paymentId,
        string $transactionType,
        ?string $participantId = null
    ): ?PaymentUnzerTransactionTransfer {
        $paymentUnzerTransactionQuery = $this->getFactory()
            ->createPaymentUnzerTransactionQuery()
            ->usePaymentUnzerQuery()
                ->filterByPaymentId($paymentId)
                ->filterByIsMarketplace(true)
            ->endUse()
            ->filterByType($transactionType);

        if ($participantId !== null) {
            $paymentUnzerTransactionQuery = $paymentUnzerTransactionQuery->filterByParticipantId($participantId);
        }
        $paymentUnzerTransactionEntity = $paymentUnzerTransactionQuery->findOne();

        if ($paymentUnzerTransactionEntity === null) {
            return null;
        }

        return $this->getFactory()->createUnzerPersistenceMapper()
            ->mapPaymentUnzerTransactionEntityToPaymentUnzerTransactionTransfer(
                $paymentUnzerTransactionEntity,
                new PaymentUnzerTransactionTransfer(),
            );
    }

    /**
     * @param int $idCustomer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer|null
     */
    public function findUnzerCustomerByIdCustomer(int $idCustomer): ?UnzerCustomerTransfer
    {
        /** @var \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerCustomer $paymentUnzerCustomerEntity */
        $paymentUnzerCustomerEntity = $this->getFactory()
            ->createPaymentUnzerCustomerQuery()
            ->useCustomerQuery()
                ->filterByIdCustomer($idCustomer)
            ->endUse()
            ->findOne();

        if (!$paymentUnzerCustomerEntity) {
            return null;
        }

        return $this->getFactory()->createUnzerPersistenceMapper()
            ->mapPaymentUnzerCustomerEntityToUnzerCustomerTransfer(
                $paymentUnzerCustomerEntity,
                new UnzerCustomerTransfer(),
            );
    }

    /**
     * @param \Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipantQuery $merchantUnzerParticipantQuery
     * @param \Generated\Shared\Transfer\MerchantUnzerParticipantConditionsTransfer $merchantUnzerParticipantConditionsTransfer
     *
     * @return \Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipantQuery
     */
    protected function setMerchantUnzerParticipantFilters(
        SpyMerchantUnzerParticipantQuery $merchantUnzerParticipantQuery,
        MerchantUnzerParticipantConditionsTransfer $merchantUnzerParticipantConditionsTransfer
    ): SpyMerchantUnzerParticipantQuery {
        if ($merchantUnzerParticipantConditionsTransfer->getReferences()) {
            $merchantUnzerParticipantQuery
                ->useMerchantQuery()
                ->filterByMerchantReference_In(
                    $merchantUnzerParticipantConditionsTransfer->getReferences(),
                )
            ->endUse();
        }

        return $merchantUnzerParticipantQuery;
    }

    /**
     * @param int $idUnzerCredentials
     *
     * @return \Generated\Shared\Transfer\StoreRelationTransfer
     */
    public function getStoreRelationByIdUnzerCredentials(int $idUnzerCredentials): StoreRelationTransfer
    {
        $unzerCredentialsStoreEntities = $this->getFactory()
            ->createUnzerCredentialsStoreQuery()
            ->filterByFkUnzerCredentials($idUnzerCredentials)
            ->leftJoinWithStore()
            ->find();

        $storeRelationTransfer = (new StoreRelationTransfer())->setIdEntity($idUnzerCredentials);

        return $this->getFactory()
            ->createUnzerPersistenceMapper()
            ->mapUnzerCredentialsStoreEntitiesToStoreRelationTransfer($unzerCredentialsStoreEntities, $storeRelationTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer
     */
    public function findUnzerCredentialsCollectionByCriteria(
        UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer
    ): UnzerCredentialsCollectionTransfer {
        $unzerCredentialsQuery = $this->getFactory()->createUnzerCredentialsQuery();
        $unzerCredentialsQuery = $this->setUnzerConfigFilters(
            $unzerCredentialsQuery,
            $unzerCredentialsCriteriaTransfer->getUnzerCredentialsConditionsOrFail(),
        );

        $unzerCredentialsEntities = $unzerCredentialsQuery->find();

        return $this->getFactory()->createUnzerPersistenceMapper()
            ->mapUnzerCredentialsEntityCollectionToUnzerCredentialsTransferCollection(
                $unzerCredentialsEntities,
                new UnzerCredentialsCollectionTransfer(),
            );
    }

    /**
     * @param \Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsQuery $unzerCredentialsQuery
     * @param \Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer $unzerCredentialsConditionsTransfer
     *
     * @return \Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsQuery
     */
    protected function setUnzerConfigFilters(
        SpyUnzerCredentialsQuery           $unzerCredentialsQuery,
        UnzerCredentialsConditionsTransfer $unzerCredentialsConditionsTransfer
    ): SpyUnzerCredentialsQuery {
        if ($unzerCredentialsConditionsTransfer->getKeypairIds()) {
            $unzerCredentialsQuery->filterByKeypairId_In($unzerCredentialsConditionsTransfer->getKeypairIds());
        }

        if ($unzerCredentialsConditionsTransfer->getMerchantReferences()) {
            $unzerCredentialsQuery->filterByMerchantReference_In($unzerCredentialsConditionsTransfer->getMerchantReferences());
        }

        if ($unzerCredentialsConditionsTransfer->getPublicKeys()) {
            $unzerCredentialsQuery->filterByPublicKey_In($unzerCredentialsConditionsTransfer->getPublicKeys());
        }

        if ($unzerCredentialsConditionsTransfer->getTypes()) {
            $unzerCredentialsQuery->filterByType_In($unzerCredentialsConditionsTransfer->getTypes());
        }

        if ($unzerCredentialsConditionsTransfer->getIds()) {
            $unzerCredentialsQuery->filterByIdUnzerCredentials_In($unzerCredentialsConditionsTransfer->getIds());
        }

        if ($unzerCredentialsConditionsTransfer->getParentIds()) {
            $unzerCredentialsQuery->filterByParentIdUnzerCredentials_In($unzerCredentialsConditionsTransfer->getParentIds());
        }

        if ($unzerCredentialsConditionsTransfer->getStoreNames()) {
            $unzerCredentialsQuery
                ->joinWithUnzerCredentialsStore()
                ->useUnzerCredentialsStoreQuery()
                    ->joinWithStore()
                    ->useStoreQuery()
                        ->filterByName_In($unzerCredentialsConditionsTransfer->getStoreNames())
                    ->endUse()
                ->endUse();
        }

        if ($unzerCredentialsConditionsTransfer->getStoreIds()) {
            $unzerCredentialsQuery
                ->joinWithUnzerCredentialsStore()
                ->useUnzerCredentialsStoreQuery()
                    ->joinWithStore()
                    ->useStoreQuery()
                        ->filterByIdStore_In($unzerCredentialsConditionsTransfer->getStoreIds())
                ->endUse()
                ->endUse();
        }

        return $unzerCredentialsQuery;
    }
}
