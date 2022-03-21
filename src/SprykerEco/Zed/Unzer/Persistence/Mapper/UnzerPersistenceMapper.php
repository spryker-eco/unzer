<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Persistence\Mapper;

use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Orm\Zed\Store\Persistence\SpyStore;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzer;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerCustomer;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItem;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerTransaction;
use Orm\Zed\Unzer\Persistence\SpyUnzerCredentials;
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
                    new PaymentUnzerOrderItemTransfer(),
                ),
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
            ->setIdSalesOrder($paymentUnzerEntity->getFkSalesOrder())
            ->setKeypairId($paymentUnzerEntity->getUnzerKeypairId());
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
            ->setFkSalesOrder($paymentUnzerTransfer->getIdSalesOrderOrFail())
            ->setUnzerKeypairId($paymentUnzerTransfer->getKeypairIdOrFail());
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
            ->setFkSalesOrderItem($paymentUnzerOrderItemTransfer->getIdSalesOrderItemOrFail())
            ->setFkPaymentUnzer($paymentUnzerOrderItemTransfer->getIdPaymentUnzerOrFail());
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
            ->setFkPaymentUnzer($paymentUnzerTransactionTransfer->getIdPaymentUnzerOrFail());
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

    /**
     * @param \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerCustomer $paymentUnzerCustomerEntity
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function mapPaymentUnzerCustomerEntityToUnzerCustomerTransfer(
        SpyPaymentUnzerCustomer $paymentUnzerCustomerEntity,
        UnzerCustomerTransfer $unzerCustomerTransfer
    ): UnzerCustomerTransfer {
        return $unzerCustomerTransfer->setId($paymentUnzerCustomerEntity->getUnzerCustomerId());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     * @param \Orm\Zed\Unzer\Persistence\SpyUnzerCredentials $unzerCredentialsEntity
     *
     * @return \Orm\Zed\Unzer\Persistence\SpyUnzerCredentials
     */
    public function mapUnzerCredentialsTransferToUnzerCredentialsEntity(
        UnzerCredentialsTransfer $unzerCredentialsTransfer,
        SpyUnzerCredentials $unzerCredentialsEntity
    ): SpyUnzerCredentials {
        return $unzerCredentialsEntity->fromArray($unzerCredentialsTransfer->toArray())
            ->setPublicKey($unzerCredentialsTransfer->getUnzerKeypairOrFail()->getPublicKeyOrFail());
    }

    /**
     * @param \Orm\Zed\Unzer\Persistence\SpyUnzerCredentials $unzerCredentialsEntity
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function mapUnzerCredentialsEntityToUnzerCredentialsTransfer(
        SpyUnzerCredentials $unzerCredentialsEntity,
        UnzerCredentialsTransfer $unzerCredentialsTransfer
    ): UnzerCredentialsTransfer {
        $unzerCredentialsTransfer = $unzerCredentialsTransfer
            ->fromArray($unzerCredentialsEntity->toArray(), true);

        if ($unzerCredentialsTransfer->getUnzerKeypair()) {
            $unzerCredentialsTransfer->getUnzerKeypairOrFail()
                ->setPublicKey($unzerCredentialsEntity->getPublicKey())
                ->setKeypairId($unzerCredentialsEntity->getKeypairId());

            return $unzerCredentialsTransfer;
        }

        return $unzerCredentialsTransfer->setUnzerKeypair(
            (new UnzerKeypairTransfer())
                ->setPublicKey($unzerCredentialsEntity->getPublicKey())
                ->setKeypairId($unzerCredentialsEntity->getKeypairId()),
        );
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $unzerCredentialsStoreEntities
     * @param \Generated\Shared\Transfer\StoreRelationTransfer $storeRelationTransfer
     *
     * @return \Generated\Shared\Transfer\StoreRelationTransfer
     */
    public function mapUnzerCredentialsStoreEntitiesToStoreRelationTransfer(
        ObjectCollection $unzerCredentialsStoreEntities,
        StoreRelationTransfer $storeRelationTransfer
    ): StoreRelationTransfer {
        foreach ($unzerCredentialsStoreEntities as $unzerCredentialsStoreEntity) {
            $storeRelationTransfer->addStores($this->mapStoreEntityToStoreTransfer($unzerCredentialsStoreEntity->getStore(), new StoreTransfer()));
            $storeRelationTransfer->addIdStores($unzerCredentialsStoreEntity->getFkStore());
        }

        return $storeRelationTransfer;
    }

    /**
     * @param \Orm\Zed\Store\Persistence\SpyStore $storeEntity
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\StoreTransfer
     */
    protected function mapStoreEntityToStoreTransfer(SpyStore $storeEntity, StoreTransfer $storeTransfer): StoreTransfer
    {
        return $storeTransfer->fromArray($storeEntity->toArray(), true);
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $unzerCredentialsEntities
     * @param \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer $unzerCredentialsCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer
     */
    public function mapUnzerCredentialsEntityCollectionToUnzerCredentialsTransferCollection(
        ObjectCollection $unzerCredentialsEntities,
        UnzerCredentialsCollectionTransfer $unzerCredentialsCollectionTransfer
    ): UnzerCredentialsCollectionTransfer {
        foreach ($unzerCredentialsEntities as $unzerCredentialsEntity) {
            $unzerCredentialsCollectionTransfer->addUnzerCredentials(
                $this->mapUnzerCredentialsEntityToUnzerCredentialsTransfer($unzerCredentialsEntity, new UnzerCredentialsTransfer()),
            );
        }

        return $unzerCredentialsCollectionTransfer;
    }
}
