<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Persistence;

use Generated\Shared\Transfer\MerchantUnzerParticipantTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipant;
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
                $paymentUnzerOrderItemEntity
            );

        $paymentUnzerOrderItemEntity->save();

        return $unzerPersistenceMapper->mapPaymentUnzerOrderItemEntityToPaymentUnzerOrderItemTransfer(
            $paymentUnzerOrderItemEntity,
            $paymentUnzerOrderItemTransfer
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
                $paymentUnzerTransactionEntity
            );

        $paymentUnzerTransactionEntity->save();

        return $unzerPersistenceMapper->mapPaymentUnzerTransactionEntityToPaymentUnzerTransactionTransfer(
            $paymentUnzerTransactionEntity,
            $paymentUnzerTransactionTransfer
        );
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer $merchantUnzerParticipantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer
     */
    public function saveMerchantUnzerParticipantEntity(MerchantUnzerParticipantTransfer $merchantUnzerParticipantTransfer): MerchantUnzerParticipantTransfer
    {
        $merchantUnzerParticipantEntity = $this->getFactory()
            ->createMerchantUnzerParticipantQuery()
            ->filterByFkMerchant($merchantUnzerParticipantTransfer->getMerchantId())
            ->findOneOrCreate();

        $merchantUnzerParticipantEntity = $this->getMapper()
            ->mapMerchantUnzerParticipantTransferToEntity($merchantUnzerParticipantTransfer, $merchantUnzerParticipantEntity);

        $merchantUnzerParticipantEntity = $this->saveOrDeleteMerchantUnzerParticipantEntity($merchantUnzerParticipantEntity);

        return $this->getMapper()
            ->mapMerchantUnzerParticipantEntityToMerchantUnzerParticipantTransfer($merchantUnzerParticipantEntity, $merchantUnzerParticipantTransfer);
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
}