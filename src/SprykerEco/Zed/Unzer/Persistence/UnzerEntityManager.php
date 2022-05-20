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
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzer;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItem;
use Orm\Zed\Unzer\Persistence\SpyUnzerCredentials;
use Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsStore;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;

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
    public function createPaymentUnzerEntity(PaymentUnzerTransfer $paymentUnzerTransfer): PaymentUnzerTransfer
    {
        $paymentUnzerEntity = $this->getFactory()
            ->getUnzerMapper()
            ->mapPaymentUnzerTransferToPaymentUnzerEntity($paymentUnzerTransfer, new SpyPaymentUnzer());

        $paymentUnzerEntity->save();

        return $this->getFactory()
            ->getUnzerMapper()
            ->mapPaymentUnzerEntityToPaymentUnzerTransfer($paymentUnzerEntity, $paymentUnzerTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    public function updatePaymentUnzerEntity(PaymentUnzerTransfer $paymentUnzerTransfer): PaymentUnzerTransfer
    {
        $paymentUnzerEntity = $this->getFactory()
            ->getPaymentUnzerQuery()
            ->filterByFkSalesOrder($paymentUnzerTransfer->getIdSalesOrder())
            ->filterByOrderId($paymentUnzerTransfer->getOrderId())
            ->findOne();

        if ($paymentUnzerEntity === null) {
            throw new UnzerException(
                sprintf('Unzer paymentTransfer for order id %s not found!', $paymentUnzerTransfer->getIdSalesOrder()),
            );
        }

        $paymentUnzerEntity = $this->getFactory()
            ->getUnzerMapper()
            ->mapPaymentUnzerTransferToPaymentUnzerEntity($paymentUnzerTransfer, $paymentUnzerEntity);

        $paymentUnzerEntity->save();

        return $this->getFactory()
            ->getUnzerMapper()
            ->mapPaymentUnzerEntityToPaymentUnzerTransfer($paymentUnzerEntity, $paymentUnzerTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer
     */
    public function createPaymentUnzerOrderItemEntity(
        PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
    ): PaymentUnzerOrderItemTransfer {
        $paymentUnzerOrderItemEntity = $this->getFactory()
            ->getUnzerMapper()
            ->mapPaymentUnzerOrderItemTransferToPaymentUnzerOrderItemEntity(
                $paymentUnzerOrderItemTransfer,
                new SpyPaymentUnzerOrderItem(),
            );

        $paymentUnzerOrderItemEntity->save();

        return $this->getFactory()
            ->getUnzerMapper()
            ->mapPaymentUnzerOrderItemEntityToPaymentUnzerOrderItemTransfer(
                $paymentUnzerOrderItemEntity,
                $paymentUnzerOrderItemTransfer,
            );
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer
     */
    public function updatePaymentUnzerOrderItemEntity(
        PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
    ): PaymentUnzerOrderItemTransfer {
        $paymentUnzerOrderItemEntity = $this->getFactory()
            ->getPaymentUnzerOrderItemQuery()
            ->filterByFkSalesOrderItem($paymentUnzerOrderItemTransfer->getIdSalesOrderItem())
            ->filterByFkPaymentUnzer($paymentUnzerOrderItemTransfer->getIdPaymentUnzer())
            ->findOne();

        if ($paymentUnzerOrderItemEntity === null) {
            throw new UnzerException(
                sprintf(
                    'Unzer payment order item entity for order id %s not found!',
                    $paymentUnzerOrderItemTransfer->getIdSalesOrderItem(),
                ),
            );
        }

        $paymentUnzerOrderItemEntity = $this->getFactory()
            ->getUnzerMapper()
            ->mapPaymentUnzerOrderItemTransferToPaymentUnzerOrderItemEntity(
                $paymentUnzerOrderItemTransfer,
                $paymentUnzerOrderItemEntity,
            );

        $paymentUnzerOrderItemEntity->save();

        return $this->getFactory()
            ->getUnzerMapper()
            ->mapPaymentUnzerOrderItemEntityToPaymentUnzerOrderItemTransfer(
                $paymentUnzerOrderItemEntity,
                $paymentUnzerOrderItemTransfer,
            );
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer
     */
    public function createPaymentUnzerTransactionEntity(
        PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
    ): PaymentUnzerTransactionTransfer {
        $paymentUnzerTransactionEntity = $this->getFactory()->getPaymentUnzerTransactionQuery()
            ->filterByTransactionUniqueId($paymentUnzerTransactionTransfer->getTransactionUniqueId())
            ->findOneOrCreate();

        if (!$paymentUnzerTransactionEntity->isNew()) {
            // Such transaction already saved, so skip
            return $paymentUnzerTransactionTransfer;
        }

        $unzerPersistenceMapper = $this->getFactory()->getUnzerMapper();

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
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function createUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsTransfer
    {
        $unzerCredentialsEntity = $this->getFactory()->getUnzerMapper()
            ->mapUnzerCredentialsTransferToUnzerCredentialsEntity($unzerCredentialsTransfer, new SpyUnzerCredentials());

        $unzerCredentialsEntity->save();

        return $this->getFactory()->getUnzerMapper()
            ->mapUnzerCredentialsEntityToUnzerCredentialsTransfer($unzerCredentialsEntity, $unzerCredentialsTransfer);
    }

    /**
     * @param array<int> $storeIds
     * @param int $idUnzerCredentials
     *
     * @return void
     */
    public function createUnzerCredentialsStoreRelationsForStores(array $storeIds, int $idUnzerCredentials): void
    {
        foreach ($storeIds as $idStore) {
            $shipmentMethodStoreEntity = new SpyUnzerCredentialsStore();
            $shipmentMethodStoreEntity->setFkStore($idStore)
                ->setFkUnzerCredentials($idUnzerCredentials)
                ->save();
        }
    }

    /**
     * @param array<int> $storeIds
     * @param int $idUnzerCredentials
     *
     * @return void
     */
    public function deleteUnzerCredentialsStoreRelationsForStores(array $storeIds, int $idUnzerCredentials): void
    {
        if ($storeIds === []) {
            return;
        }

        $this->getFactory()
            ->getUnzerCredentialsStoreQuery()
            ->filterByFkUnzerCredentials($idUnzerCredentials)
            ->filterByFkStore_In($storeIds)
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
            ->getUnzerCredentialsQuery()
            ->filterByIdUnzerCredentials($unzerCredentialsTransfer->getIdUnzerCredentialsOrFail())
            ->findOne();

        if ($unzerCredentialsEntity === null) {
            return null;
        }

        $unzerCredentialsEntity = $this->getFactory()->getUnzerMapper()
            ->mapUnzerCredentialsTransferToUnzerCredentialsEntity($unzerCredentialsTransfer, $unzerCredentialsEntity);
        $unzerCredentialsEntity->save();

        return $this->getFactory()->getUnzerMapper()
            ->mapUnzerCredentialsEntityToUnzerCredentialsTransfer($unzerCredentialsEntity, $unzerCredentialsTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return bool
     */
    public function deleteUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): bool
    {
        $deletedRowsCount = $this->getFactory()
            ->getUnzerCredentialsQuery()
            ->filterByIdUnzerCredentials($unzerCredentialsTransfer->getIdUnzerCredentials())
            ->delete();

        return $deletedRowsCount !== 0;
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
}
