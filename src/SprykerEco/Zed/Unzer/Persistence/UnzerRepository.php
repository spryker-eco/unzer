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
use Generated\Shared\Transfer\UnzerConfigCollectionTransfer;
use Generated\Shared\Transfer\UnzerConfigConditionsTransfer;
use Generated\Shared\Transfer\UnzerConfigCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipantQuery;
use Orm\Zed\Unzer\Persistence\SpyUnzerConfigQuery;
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
     * @param string $unzerPaymentId
     * @param string $keypairId
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer|null
     */
    public function findPaymentUnzerByPaymentIdAndKeypairId(string $unzerPaymentId, string $keypairId): ?PaymentUnzerTransfer
    {
        $paymentUnzerEntity = $this->getFactory()->createPaymentUnzerQuery()
            ->filterByPaymentId($unzerPaymentId)
            ->filterByUnzerKeypairId($keypairId)
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

        if ($paymentUnzerCustomerEntity === null) {
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
     * @param int $idUnzerConfig
     *
     * @return \Generated\Shared\Transfer\StoreRelationTransfer
     */
    public function getStoreRelationByIdPaymentMethod(int $idUnzerConfig): StoreRelationTransfer
    {
        $unzerConfigStoreEntities = $this->getFactory()
            ->createUnzerConfigStoreQuery()
            ->filterByFkUnzerConfig($idUnzerConfig)
            ->leftJoinWithStore()
            ->find();

        $storeRelationTransfer = (new StoreRelationTransfer())->setIdEntity($idUnzerConfig);

        return $this->getFactory()
            ->createUnzerPersistenceMapper()
            ->mapUnzerConfigStoreEntitiesToStoreRelationTransfer($unzerConfigStoreEntities, $storeRelationTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerConfigCriteriaTransfer $unzerConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigCollectionTransfer
     */
    public function findUnzerConfigsByCriteria(UnzerConfigCriteriaTransfer $unzerConfigCriteriaTransfer): UnzerConfigCollectionTransfer
    {
        $unzerConfigQuery = $this->getFactory()->createUnzerConfigQuery();
        $unzerConfigQuery = $this->setUnzerConfigFilters(
            $unzerConfigQuery,
            $unzerConfigCriteriaTransfer->getUnzerConfigConditions(),
        );

        $unzerConfigEntities = $unzerConfigQuery->find();

        return $this->getFactory()->createUnzerPersistenceMapper()
            ->mapUnzerConfigEntityCollectionToUnzerConfigTransferCollection(
                $unzerConfigEntities,
                new UnzerConfigCollectionTransfer(),
            );
    }

    /**
     * @param \Orm\Zed\Unzer\Persistence\SpyUnzerConfigQuery $unzerConfigQuery
     * @param \Generated\Shared\Transfer\UnzerConfigConditionsTransfer $unzerConfigConditionsTransfer
     *
     * @return \Orm\Zed\Unzer\Persistence\SpyUnzerConfigQuery
     */
    protected function setUnzerConfigFilters(
        SpyUnzerConfigQuery $unzerConfigQuery,
        UnzerConfigConditionsTransfer $unzerConfigConditionsTransfer
    ): SpyUnzerConfigQuery {
        if ($unzerConfigConditionsTransfer->getKeypairIds() !== null) {
            $unzerConfigQuery->filterByKeypairId_In($unzerConfigConditionsTransfer->getKeypairIds());
        }

        if ($unzerConfigConditionsTransfer->getMerchantReferences() !== null) {
            $unzerConfigQuery->filterByMerchantReference_In($unzerConfigConditionsTransfer->getMerchantReferences());
        }

        if ($unzerConfigConditionsTransfer->getPublicKeys() !== null) {
            $unzerConfigQuery->filterByPublicKey_In($unzerConfigConditionsTransfer->getPublicKeys());
        }

        if ($unzerConfigConditionsTransfer->getTypes() !== null) {
            $unzerConfigQuery->filterByType_In($unzerConfigConditionsTransfer->getTypes());
        }

        if ($unzerConfigConditionsTransfer->getStoreNames() !== null) {
            $unzerConfigQuery
                ->joinWithUnzerConfigStore()
                ->useUnzerConfigStoreQuery()
                    ->joinWithStore()
                    ->useStoreQuery()
                        ->filterByName_In($unzerConfigConditionsTransfer->getStoreNames())
                    ->endUse()
                ->endUse();
        }

        return $unzerConfigQuery;
    }
}
