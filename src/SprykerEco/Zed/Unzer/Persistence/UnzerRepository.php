<?php

namespace SprykerEco\Zed\Unzer\Persistence;

use Generated\Shared\Transfer\MerchantUnzerParticipantTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Pyz\Zed\Unzer\Persistence\Mapper\UnzerPersistenceMapper;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerPersistenceFactory getFactory()
 */
class UnzerRepository extends AbstractRepository implements UnzerRepositoryInterface
{
    /**
     * @param string $merchantReference
     *
     * @return \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer|null
     */
    public function findMerchantUnzerParticipantByMerchantReference(string $merchantReference): ?MerchantUnzerParticipantTransfer
    {
        /** @var \Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipant|null $merchantUnzerParticipantEntity */
        $merchantUnzerParticipantEntity = $this->getFactory()->createMerchantUnzerParticipantQuery()
            ->useMerchantQuery()
            ->filterByMerchantReference($merchantReference)
            ->endUse()
            ->findOne();

        if ($merchantUnzerParticipantEntity === null) {
            return null;
        }

        return $this->getMapper()
            ->mapMerchantUnzerParticipantEntityToMerchantUnzerParticipantTransfer(
                $merchantUnzerParticipantEntity,
                new MerchantUnzerParticipantTransfer()
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

        return $this->getMapper()->mapPaymentUnzerEntityToPaymentUnzerTransfer(
            $paymentUnzerEntity,
            new PaymentUnzerTransfer()
        );
    }

    /**
     * @param string $orderId
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer
     */
    public function findPaymentUnzerOrderItemCollectionByOrderId(string $orderId): PaymentUnzerOrderItemCollectionTransfer
    {
        $paymentUnzerOrderItemEntities = $this->getFactory()
            ->createPaymentUnzerOrderItemQuery()
            ->usePaymentUnzerQuery()
            ->filterByOrderId($orderId)
            ->endUse()
            ->find();

        return $this
            ->getMapper()
            ->mapPaymentUnzerOrderItemEntitiesToPaymentUnzerOrderItemCollectionTransfer(
                $paymentUnzerOrderItemEntities,
                new PaymentUnzerOrderItemCollectionTransfer()
            );
    }

    /**
     * @return \Pyz\Zed\Unzer\Persistence\Mapper\UnzerPersistenceMapper
     */
    protected function getMapper(): UnzerPersistenceMapper
    {
        return $this->getFactory()->createUnzerPersistenceMapper();
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

        return $this->getMapper()
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

        return $this->getMapper()
            ->mapPaymentUnzerOrderItemEntityToPaymentUnzerOrderItemTransfer(
                $paymentUnzerOrderItemEntity,
                new PaymentUnzerOrderItemTransfer()
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

        return $this->getMapper()
            ->mapPaymentUnzerTransactionEntityToPaymentUnzerTransactionTransfer(
                $paymentUnzerTransactionEntity,
                new PaymentUnzerTransactionTransfer()
            );
    }
}
