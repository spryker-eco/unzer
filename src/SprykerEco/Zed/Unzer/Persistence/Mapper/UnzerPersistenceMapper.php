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
        return $paymentUnzerOrderItemTransfer
            ->fromArray($paymentUnzerOrderItemEntity->toArray(), true)
            ->setIdPaymentUnzer($paymentUnzerOrderItemEntity->getFkPaymentUnzer())
            ->setIdSalesOrderItem($paymentUnzerOrderItemEntity->getFkSalesOrderItem());
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
        return $merchantUnzerParticipantTransfer
            ->fromArray($merchantUnzerParticipantEntity->toArray(), true)
            ->setMerchantId($merchantUnzerParticipantEntity->getFkMerchant());
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
        return $merchantUnzerParticipantEntity
            ->fromArray($merchantUnzerParticipantTransfer->toArray())
            ->setFkMerchant($merchantUnzerParticipantTransfer->getMerchantId());
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
        return $paymentUnzerTransfer
            ->fromArray($paymentUnzerEntity->toArray(), true)
            ->setIdSalesOrder($paymentUnzerEntity->getFkSalesOrder());
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
        return $paymentUnzerEntity
            ->fromArray($paymentUnzerTransfer->toArray())
            ->setFkSalesOrder($paymentUnzerTransfer->getIdSalesOrder());
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
        return $paymentUnzerOrderItemEntity
            ->fromArray($paymentUnzerOrderItemTransfer->toArray())
            ->setFkSalesOrderItem($paymentUnzerOrderItemTransfer->getIdSalesOrderItem())
            ->setFkPaymentUnzer($paymentUnzerOrderItemTransfer->getIdPaymentUnzer());
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
        return $paymentUnzerTransactionEntity
            ->fromArray($paymentUnzerTransactionTransfer->toArray())
            ->setFkPaymentUnzer($paymentUnzerTransactionTransfer->getIdPaymentUnzer());
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
        return $paymentUnzerTransactionTransfer
            ->fromArray($paymentUnzerTransactionEntity->toArray(), true)
            ->setIdPaymentUnzer($paymentUnzerTransactionEntity->getFkPaymentUnzer());
    }
}
