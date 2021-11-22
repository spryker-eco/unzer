<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Persistence;

use Generated\Shared\Transfer\MerchantUnzerParticipantCollectionTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantConditionsTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantCriteriaTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipantQuery;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerPersistenceFactory getFactory()
 */
class UnzerRepository extends AbstractRepository implements UnzerRepositoryInterface
{
    /**
     * @param \Generated\Shared\Transfer\MerchantUnzerParticipantCriteriaTransfer $merchantUnzerParticipantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUnzerParticipantCollectionTransfer
     */
    public function findMerchantUnzerParticipantByCriteria(
        MerchantUnzerParticipantCriteriaTransfer $merchantUnzerParticipantCriteriaTransfer
    ): MerchantUnzerParticipantCollectionTransfer {
        $merchantUnzerParticipantQuery = $this->getFactory()->createMerchantUnzerParticipantQuery();
        $merchantUnzerParticipantQuery = $this->setMerchantUnzerParticipantFilters(
            $merchantUnzerParticipantQuery,
            $merchantUnzerParticipantCriteriaTransfer->getMerchantUnzerParticipantConditions(),
        );

        $merchantUnzerParticipantEntities = $merchantUnzerParticipantQuery->find();

        return $this->getFactory()->createUnzerPersistenceMapper()
            ->mapMerchantUnzerParticipantEntityCollectionToMerchantUnzerParticipantTransferCollection(
                $merchantUnzerParticipantEntities,
                new MerchantUnzerParticipantCollectionTransfer(),
            );
    }

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
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer|null
     */
    public function findPaymentUnzerByPaymentId(string $unzerPaymentId): ?PaymentUnzerTransfer
    {
        $paymentUnzerEntity = $this->getFactory()->createPaymentUnzerQuery()
            ->filterByPaymentId($unzerPaymentId)
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
     * @param string $merchantReference
     * @param int $idStore
     *
     * @return string|null
     */
    public function findUnzerVaultKeyByMerchantReferenceAndIdStore(string $merchantReference, int $idStore): ?string
    {
        $merchantUnzerVaultEntity = $this->getFactory()
            ->createMerchantUnzerVaultQuery()
            ->useMerchantQuery()
                ->filterByMerchantReference($merchantReference)
            ->endUse()
            ->filterByFkStore($idStore)
            ->findOne();

        if ($merchantUnzerVaultEntity === null) {
            return null;
        }

        return $merchantUnzerVaultEntity->getVaultKey();
    }

    /**
     * @param string $vaultKey
     *
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer|null
     */
    public function findUnzerKeypairByKeypairId(string $vaultKey): ?UnzerKeypairTransfer
    {
        $unzerKeypairEntity = $this->getFactory()
            ->createUnzerKeypairQuery()
            ->filterByKeypairId($vaultKey)
            ->findOne();

        if ($unzerKeypairEntity === null) {
            return null;
        }

        return $this->getFactory()->createUnzerPersistenceMapper()
            ->mapUnzerKeypairEntityToUnzerKeypairTransfer(
                $unzerKeypairEntity,
                new UnzerKeypairTransfer(),
            );
    }
}
