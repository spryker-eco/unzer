<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Persistence;

use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerConfigTransfer;
use Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipant;
use Orm\Zed\Unzer\Persistence\SpyUnzerConfig;
use Orm\Zed\Unzer\Persistence\SpyUnzerConfigStore;
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
     * @param \Generated\Shared\Transfer\UnzerConfigTransfer $unzerConfigTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigTransfer
     */
    public function createUnzerConfig(UnzerConfigTransfer $unzerConfigTransfer): UnzerConfigTransfer
    {
        $unzerConfigEntity = $this->getMapper()
            ->mapUnzerConfigTransferToUnzerConfigEntity($unzerConfigTransfer, new SpyUnzerConfig());

        $unzerConfigEntity->save();

        return $this->getMapper()
            ->mapUnzerConfigEntityToUnzerConfigTransfer($unzerConfigEntity, $unzerConfigTransfer);
    }

    /**
     * @param array $idStores
     * @param int $idUnzerConfig
     *
     * @return void
     */
    public function addUnzerConfigStoreRelationsForStores(array $idStores, int $idUnzerConfig): void
    {
        foreach ($idStores as $idStore) {
            $shipmentMethodStoreEntity = new SpyUnzerConfigStore();
            $shipmentMethodStoreEntity->setFkStore($idStore)
                ->setFkUnzerConfig($idUnzerConfig)
                ->save();
        }
    }

    /**
     * @param array $idStores
     * @param int $idUnzerConfig
     *
     * @return void
     */
    public function removeUnzerConfigStoreRelationsForStores(array $idStores, int $idUnzerConfig): void
    {
        if ($idStores === []) {
            return;
        }

        $this->getFactory()
            ->createUnzerConfigStoreQuery()
            ->filterByFkUnzerConfig($idUnzerConfig)
            ->filterByFkStore_In($idStores)
            ->delete();
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerConfigTransfer $unzerConfigTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigTransfer|null
     */
    public function updateUnzerConfig(UnzerConfigTransfer $unzerConfigTransfer): ?UnzerConfigTransfer
    {
        $unzerConfigEntity = $this->getFactory()
            ->createUnzerConfigQuery()
            ->filterByIdUnzerConfig($unzerConfigTransfer->getIdUnzerConfigOrFail())
            ->findOne();

        if ($unzerConfigEntity === null) {
            return null;
        }

        $unzerConfigEntity = $this->getMapper()->mapUnzerConfigTransferToUnzerConfigEntity($unzerConfigTransfer, $unzerConfigEntity);
        $unzerConfigEntity->save();

        return $this->getMapper()->mapUnzerConfigEntityToUnzerConfigTransfer($unzerConfigEntity, $unzerConfigTransfer);
    }
}
