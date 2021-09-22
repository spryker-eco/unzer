<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Persistence\Mapper;

use Generated\Shared\Transfer\MerchantUnzerParticipantTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipant;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzer;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItem;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerTransaction;
use Propel\Runtime\Collection\ObjectCollection;

class UnzerPersistenceMapper
{
    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $paymentUnzerOrderItemEntities
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer
     */
    public function mapPaymentUnzerOrderItemEntitiesToPaymentUnzerOrderItemCollectionTransfer(
        ObjectCollection $paymentUnzerOrderItemEntities,
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
    ): PaymentUnzerOrderItemCollectionTransfer {
        foreach ($paymentUnzerOrderItemEntities as $paymentUnzerOrderItemEntity) {
            $paymentUnzerOrderItemCollectionTransfer->addPaymentUnzerOrderItem(
                $this->mapPaymentUnzerOrderItemEntityToPaymentUnzerOrderItemTransfer(
                    $paymentUnzerOrderItemEntity,
                    new PaymentUnzerOrderItemTransfer()
                )
            );
        }

        return $paymentUnzerOrderItemCollectionTransfer;
    }

    /**
     * @param \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItem $paymentUnzerOrderItemEntity
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer
     */
    public function mapPaymentUnzerOrderItemEntityToPaymentUnzerOrderItemTransfer(
        SpyPaymentUnzerOrderItem $paymentUnzerOrderItemEntity,
        PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
    ): PaymentUnzerOrderItemTransfer {
        $paymentUnzerOrderItemTransfer->fromArray($paymentUnzerOrderItemEntity->toArray(), true);
        $paymentUnzerOrderItemTransfer->setIdPaymentUnzer($paymentUnzerOrderItemEntity->getFkPaymentUnzer());
        $paymentUnzerOrderItemTransfer->setIdSalesOrderItem($paymentUnzerOrderItemEntity->getFkSalesOrderItem());

        return $paymentUnzerOrderItemTransfer;
    }

    /**
     * @param \Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipant $merchantUnzerParticipantEntity
     * @param \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer $merchantUnzerParticipantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer
     */
    public function mapMerchantUnzerParticipantEntityToMerchantUnzerParticipantTransfer(
        SpyMerchantUnzerParticipant $merchantUnzerParticipantEntity,
        MerchantUnzerParticipantTransfer $merchantUnzerParticipantTransfer
    ): MerchantUnzerParticipantTransfer {
        $merchantUnzerParticipantTransfer->fromArray($merchantUnzerParticipantEntity->toArray(), true);
        $merchantUnzerParticipantTransfer->setMerchantId($merchantUnzerParticipantEntity->getFkMerchant());

        return $merchantUnzerParticipantTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer $merchantUnzerParticipantTransfer
     * @param \Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipant $merchantUnzerParticipantEntity
     *
     * @return \Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipant
     */
    public function mapMerchantUnzerParticipantTransferToEntity(
        MerchantUnzerParticipantTransfer $merchantUnzerParticipantTransfer,
        SpyMerchantUnzerParticipant $merchantUnzerParticipantEntity
    ): SpyMerchantUnzerParticipant {
        $merchantUnzerParticipantEntity->fromArray($merchantUnzerParticipantTransfer->toArray());
        $merchantUnzerParticipantEntity->setFkMerchant($merchantUnzerParticipantTransfer->getMerchantId());

        return $merchantUnzerParticipantEntity;
    }

    /**
     * @param \Orm\Zed\Unzer\Persistence\SpyPaymentUnzer $paymentUnzerEntity
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    public function mapPaymentUnzerEntityToPaymentUnzerTransfer(
        SpyPaymentUnzer $paymentUnzerEntity,
        PaymentUnzerTransfer $paymentUnzerTransfer
    ): PaymentUnzerTransfer {
        $paymentUnzerTransfer->fromArray($paymentUnzerEntity->toArray(), true);
        $paymentUnzerTransfer->setIdSalesOrder($paymentUnzerEntity->getFkSalesOrder());

        return $paymentUnzerTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Orm\Zed\Unzer\Persistence\SpyPaymentUnzer $paymentUnzerEntity
     *
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzer
     */
    public function mapPaymentUnzerTransferToPaymentUnzerEntity(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        SpyPaymentUnzer $paymentUnzerEntity
    ): SpyPaymentUnzer {
        $paymentUnzerEntity->fromArray($paymentUnzerTransfer->toArray());
        $paymentUnzerEntity->setFkSalesOrder($paymentUnzerTransfer->getIdSalesOrder());

        return $paymentUnzerEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
     * @param \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItem $paymentUnzerOrderItemEntity
     *
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItem
     */
    public function mapPaymentUnzerOrderItemTransferToPaymentUnzerOrderItemEntity(
        PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer,
        SpyPaymentUnzerOrderItem $paymentUnzerOrderItemEntity
    ): SpyPaymentUnzerOrderItem {
        $paymentUnzerOrderItemEntity->fromArray($paymentUnzerOrderItemTransfer->toArray());
        $paymentUnzerOrderItemEntity->setFkSalesOrderItem($paymentUnzerOrderItemTransfer->getIdSalesOrderItem());
        $paymentUnzerOrderItemEntity->setFkPaymentUnzer($paymentUnzerOrderItemTransfer->getIdPaymentUnzer());

        return $paymentUnzerOrderItemEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
     * @param \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerTransaction $paymentUnzerTransactionEntity
     *
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerTransaction
     */
    public function mapPaymentUnzerTransactionTransferToPaymentUnzerTransactionEntity(
        PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer,
        SpyPaymentUnzerTransaction $paymentUnzerTransactionEntity
    ): SpyPaymentUnzerTransaction {
        $paymentUnzerTransactionEntity->fromArray($paymentUnzerTransactionTransfer->toArray());
        $paymentUnzerTransactionEntity->setFkPaymentUnzer($paymentUnzerTransactionTransfer->getIdPaymentUnzer());

        return $paymentUnzerTransactionEntity;
    }

    /**
     * @param \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerTransaction $paymentUnzerTransactionEntity
     * @param \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer
     */
    public function mapPaymentUnzerTransactionEntityToPaymentUnzerTransactionTransfer(
        SpyPaymentUnzerTransaction $paymentUnzerTransactionEntity,
        PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
    ): PaymentUnzerTransactionTransfer {
        $paymentUnzerTransactionTransfer->fromArray($paymentUnzerTransactionEntity->toArray(), true);
        $paymentUnzerTransactionTransfer->setIdPaymentUnzer($paymentUnzerTransactionEntity->getFkPaymentUnzer());

        return $paymentUnzerTransactionTransfer;
    }
}
