<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Persistence;

use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipant;
use Orm\Zed\Unzer\Persistence\SpyUnzerCredentials;
use Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsStore;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;
use SprykerEco\Zed\Unzer\Persistence\Mapper\UnzerPersistenceMapper;

/**
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerPersistenceFactory getFactory()
 */
class UnzerEntityManager extends AbstractEntityManager implements UnzerEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    public function savePaymentUnzerEntity(PaymentUnzerTransfer $paymentUnzerTransfer): PaymentUnzerTransfer
    {
        $paymentUnzerEntity = $this->getFactory()
            ->createPaymentUnzerQuery()
            ->filterByFkSalesOrder($paymentUnzerTransfer->getIdSalesOrder())
            ->filterByOrderId($paymentUnzerTransfer->getOrderId())
            ->findOneOrCreate();

        $UnzerPersistenceMapper = $this->getFactory()->createUnzerPersistenceMapper();

        $paymentUnzerEntity = $UnzerPersistenceMapper
            ->mapPaymentUnzerTransferToPaymentUnzerEntity($paymentUnzerTransfer, $paymentUnzerEntity);
        $paymentUnzerEntity->save();

        return $UnzerPersistenceMapper
            ->mapPaymentUnzerEntityToPaymentUnzerTransfer($paymentUnzerEntity, $paymentUnzerTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer
     */
    public function savePaymentUnzerOrderItemEntity(
        PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
    ): PaymentUnzerOrderItemTransfer {
        /** @var \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItem $paymentUnzerOrderItemEntity */
        $paymentUnzerOrderItemEntity = $this->getFactory()
            ->createPaymentUnzerOrderItemQuery()
            ->filterByFkSalesOrderItem($paymentUnzerOrderItemTransfer->getIdSalesOrderItem())
            ->filterByFkPaymentUnzer($paymentUnzerOrderItemTransfer->getIdPaymentUnzer())
            ->findOneOrCreate();

        $unzerPersistenceMapper = $this->getFactory()->createUnzerPersistenceMapper();

        $paymentUnzerOrderItemEntity = $unzerPersistenceMapper
            ->mapPaymentUnzerOrderItemTransferToPaymentUnzerOrderItemEntity(
                $paymentUnzerOrderItemTransfer,
                $paymentUnzerOrderItemEntity,
            );

        $paymentUnzerOrderItemEntity->save();

        return $unzerPersistenceMapper->mapPaymentUnzerOrderItemEntityToPaymentUnzerOrderItemTransfer(
            $paymentUnzerOrderItemEntity,
            $paymentUnzerOrderItemTransfer,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer
     */
    public function savePaymentUnzerTransactionEntity(
        PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
    ): PaymentUnzerTransactionTransfer {
        $paymentUnzerTransactionEntity = $this->getFactory()->createPaymentUnzerTransactionQuery()
            ->filterByTransactionUniqueId($paymentUnzerTransactionTransfer->getTransactionUniqueId())
            ->findOneOrCreate();

        if (!$paymentUnzerTransactionEntity->isNew()) {
            // Such transaction already saved, so skip
            return $paymentUnzerTransactionTransfer;
        }

        $unzerPersistenceMapper = $this->getFactory()->createUnzerPersistenceMapper();

        $paymentUnzerTransactionEntity = $unzerPersistenceMapper
            ->mapPaymentUnzerTransactionTransferToPaymentUnzerTransactionEntity(
                $paymentUnzerTransactionTransfer,
                $paymentUnzerTransactionEntity,
            );

        $paymentUnzerTransactionEntity->save();

        return $unzerPersistenceMapper->mapPaymentUnzerTransactionEntityToPaymentUnzerTransactionTransfer(
            $paymentUnzerTransactionEntity,
            $paymentUnzerTransactionTransfer,
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Persistence\Mapper\UnzerPersistenceMapper
     */
    protected function getMapper(): UnzerPersistenceMapper
    {
        return $this->getFactory()->createUnzerPersistenceMapper();
    }

    /**
     * @param \Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipant $merchantUnzerParticipantEntity
     *
     * @return \Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipant
     */
    protected function saveOrDeleteMerchantUnzerParticipantEntity(SpyMerchantUnzerParticipant $merchantUnzerParticipantEntity): SpyMerchantUnzerParticipant
    {
        if ($merchantUnzerParticipantEntity->getParticipantId()) {
            $merchantUnzerParticipantEntity->save();

            return $merchantUnzerParticipantEntity;
        }

        if ($merchantUnzerParticipantEntity->getIdMerchantUnzerParticipant()) {
            $merchantUnzerParticipantEntity->delete();

            return $merchantUnzerParticipantEntity;
        }

        return $merchantUnzerParticipantEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function createUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsTransfer
    {
        $unzerCredentialsEntity = $this->getMapper()
            ->mapUnzerCredentialsTransferToUnzerCredentialsEntity($unzerCredentialsTransfer, new SpyUnzerCredentials());

        $unzerCredentialsEntity->save();

        return $this->getMapper()
            ->mapUnzerCredentialsEntityToUnzerCredentialsTransfer($unzerCredentialsEntity, $unzerCredentialsTransfer);
    }

    /**
     * @param array<int> $idStores
     * @param int $idUnzerCredentials
     *
     * @return void
     */
    public function createUnzerCredentialsStoreRelationsForStores(array $idStores, int $idUnzerCredentials): void
    {
        foreach ($idStores as $idStore) {
            $shipmentMethodStoreEntity = new SpyUnzerCredentialsStore();
            $shipmentMethodStoreEntity->setFkStore($idStore)
                ->setFkUnzerCredentials($idUnzerCredentials)
                ->save();
        }
    }

    /**
     * @param array<int> $idStores
     * @param int $idUnzerCredentials
     *
     * @return void
     */
    public function deleteUnzerCredentialsStoreRelationsForStores(array $idStores, int $idUnzerCredentials): void
    {
        if ($idStores === []) {
            return;
        }

        $this->getFactory()
            ->createUnzerCredentialsStoreQuery()
            ->filterByFkUnzerCredentials($idUnzerCredentials)
            ->filterByFkStore_In($idStores)
            ->delete();
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer|null
     */
    public function updateUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): ?UnzerCredentialsTransfer
    {
        $unzerCredentialsEntity = $this->getFactory()
            ->createUnzerCredentialsQuery()
            ->filterByIdUnzerCredentials($unzerCredentialsTransfer->getIdUnzerCredentialsOrFail())
            ->findOne();

        if ($unzerCredentialsEntity === null) {
            return null;
        }

        $unzerCredentialsEntity = $this->getMapper()->mapUnzerCredentialsTransferToUnzerCredentialsEntity($unzerCredentialsTransfer, $unzerCredentialsEntity);
        $unzerCredentialsEntity->save();

        return $this->getMapper()->mapUnzerCredentialsEntityToUnzerCredentialsTransfer($unzerCredentialsEntity, $unzerCredentialsTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return bool
     */
    public function deleteUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): bool
    {
        $deletedRowsCount = $this->getFactory()
            ->createUnzerCredentialsQuery()
            ->filterByIdUnzerCredentials($unzerCredentialsTransfer->getIdUnzerCredentials())
            ->delete();

        return $deletedRowsCount !== 0;
    }
}
