<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Persistence;

use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionConditionsTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCriteriaTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerCustomer;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerTransactionQuery;
use Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsQuery;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method UnzerPersistenceFactory getFactory()
 */
class UnzerRepository extends AbstractRepository implements UnzerRepositoryInterface
{
    /**
     * @param string $orderReference
     *
     * @return PaymentUnzerTransfer|null
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
     * @return PaymentUnzerOrderItemCollectionTransfer
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
     * @return PaymentUnzerTransfer|null
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
     * @return PaymentUnzerOrderItemTransfer|null
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
     * @return PaymentUnzerTransactionTransfer|null
     */
    public function findPaymentUnzerTransactionByPaymentIdAndParticipantId(
        string  $paymentId,
        string  $transactionType,
        ?string $participantId = null
    ): ?PaymentUnzerTransactionTransfer
    {
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
     * @return UnzerCustomerTransfer|null
     */
    public function findUnzerCustomerByIdCustomer(int $idCustomer): ?UnzerCustomerTransfer
    {
        /** @var SpyPaymentUnzerCustomer $paymentUnzerCustomerEntity */
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
     * @param int $idUnzerCredentials
     *
     * @return StoreRelationTransfer
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
     * @param UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer
     *
     * @return UnzerCredentialsCollectionTransfer
     */
    public function findUnzerCredentialsCollectionByCriteria(
        UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer
    ): UnzerCredentialsCollectionTransfer
    {
        $unzerCredentialsQuery = $this->getFactory()->createUnzerCredentialsQuery();
        $unzerCredentialsQuery = $this->setUnzerCredentialsFilters(
            $unzerCredentialsQuery,
            $unzerCredentialsCriteriaTransfer->getUnzerCredentialsConditions(),
        );

        $unzerCredentialsEntities = $unzerCredentialsQuery->find();

        return $this->getFactory()->createUnzerPersistenceMapper()
            ->mapUnzerCredentialsEntityCollectionToUnzerCredentialsTransferCollection(
                $unzerCredentialsEntities,
                new UnzerCredentialsCollectionTransfer(),
            );
    }

    /**
     * @param PaymentUnzerTransactionCriteriaTransfer $paymentUnzerTransactionCriteriaTransfer
     *
     * @return PaymentUnzerTransactionCollectionTransfer
     */
    public function findPaymentUnzerTransactionCollectionByCriteria(
        PaymentUnzerTransactionCriteriaTransfer $paymentUnzerTransactionCriteriaTransfer
    ): PaymentUnzerTransactionCollectionTransfer
    {
        $paymentUnzerTransactionQuery = $this->getFactory()->createPaymentUnzerTransactionQuery();
        $paymentUnzerTransactionQuery = $this->setPaymentUnzerTransactionFilters(
            $paymentUnzerTransactionQuery,
            $paymentUnzerTransactionCriteriaTransfer->getPaymentUnzerTransactionConditions(),
        );

        $paymentUnzerTransactionEntities = $paymentUnzerTransactionQuery->find();

        return $this->getFactory()->createUnzerPersistenceMapper()
            ->mapPaymentUnzerTransactionEntityCollectionToPaymentUnzerTransactionCollectionTransfer(
                $paymentUnzerTransactionEntities,
                new PaymentUnzerTransactionCollectionTransfer(),
            );
    }

    /**
     * @param SpyUnzerCredentialsQuery $unzerConfigQuery
     * @param UnzerCredentialsConditionsTransfer $unzerCredentialsConditionsTransfer
     *
     * @return SpyUnzerCredentialsQuery
     */
    protected function setUnzerCredentialsFilters(
        SpyUnzerCredentialsQuery           $unzerConfigQuery,
        UnzerCredentialsConditionsTransfer $unzerCredentialsConditionsTransfer
    ): SpyUnzerCredentialsQuery
    {
        if ($unzerCredentialsConditionsTransfer->getKeypairIds()) {
            $unzerConfigQuery->filterByKeypairId_In($unzerCredentialsConditionsTransfer->getKeypairIds());
        }

        if ($unzerCredentialsConditionsTransfer->getMerchantReferences()) {
            $unzerConfigQuery->filterByMerchantReference_In($unzerCredentialsConditionsTransfer->getMerchantReferences());
        }

        if ($unzerCredentialsConditionsTransfer->getPublicKeys()) {
            $unzerConfigQuery->filterByPublicKey_In($unzerCredentialsConditionsTransfer->getPublicKeys());
        }

        if ($unzerCredentialsConditionsTransfer->getTypes()) {
            $unzerConfigQuery->filterByType_In($unzerCredentialsConditionsTransfer->getTypes());
        }

        if ($unzerCredentialsConditionsTransfer->getStoreNames()) {
            $unzerConfigQuery
                ->joinWithUnzerCredentialsStore()
                ->useUnzerCredentialsStoreQuery()
                ->joinWithStore()
                ->useStoreQuery()
                ->filterByName_In($unzerCredentialsConditionsTransfer->getStoreNames())
                ->endUse()
                ->endUse();
        }

        return $unzerConfigQuery;
    }

    /**
     * @param SpyPaymentUnzerTransactionQuery $paymentUnzerTransactionQuery
     * @param PaymentUnzerTransactionConditionsTransfer $paymentUnzerTransactionConditionsTransfer
     *
     * @return SpyPaymentUnzerTransactionQuery
     */
    protected function setPaymentUnzerTransactionFilters(
        SpyPaymentUnzerTransactionQuery           $paymentUnzerTransactionQuery,
        PaymentUnzerTransactionConditionsTransfer $paymentUnzerTransactionConditionsTransfer
    ): SpyPaymentUnzerTransactionQuery
    {
        if ($paymentUnzerTransactionConditionsTransfer->getIds()) {
            $paymentUnzerTransactionQuery->filterByIdPaymentUnzerTransaction_In($paymentUnzerTransactionConditionsTransfer->getIds());
        }

        if ($paymentUnzerTransactionConditionsTransfer->getTypes()) {
            $paymentUnzerTransactionQuery->filterByType_In($paymentUnzerTransactionConditionsTransfer->getTypes());
        }

        if ($paymentUnzerTransactionConditionsTransfer->getFkPaymentUnzerIds()) {
            $paymentUnzerTransactionQuery->filterByFkPaymentUnzer_In($paymentUnzerTransactionConditionsTransfer->getFkPaymentUnzerIds());
        }

        if ($paymentUnzerTransactionConditionsTransfer->getParticipantIds()) {
            $paymentUnzerTransactionQuery->filterByParticipantId_In($paymentUnzerTransactionConditionsTransfer->getParticipantIds());
        }

        if ($paymentUnzerTransactionConditionsTransfer->getStatuses()) {
            $paymentUnzerTransactionQuery->filterByStatus_In($paymentUnzerTransactionConditionsTransfer->getStatuses());
        }

        return $paymentUnzerTransactionQuery;
    }

}
