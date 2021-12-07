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
use Generated\Shared\Transfer\UnzerConfigCollectionTransfer;
use Generated\Shared\Transfer\UnzerConfigTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzer;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerCustomer;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItem;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerTransaction;
use Orm\Zed\Unzer\Persistence\SpyUnzerConfig;
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
        ObjectCollection                        $paymentUnzerOrderItemEntities,
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
    ): PaymentUnzerOrderItemCollectionTransfer
    {
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
        SpyPaymentUnzerOrderItem      $paymentUnzerOrderItemEntity,
        PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
    ): PaymentUnzerOrderItemTransfer
    {
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
        SpyPaymentUnzer      $paymentUnzerEntity,
        PaymentUnzerTransfer $paymentUnzerTransfer
    ): PaymentUnzerTransfer
    {
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
        SpyPaymentUnzer      $paymentUnzerEntity
    ): SpyPaymentUnzer
    {
        return $paymentUnzerEntity
            ->fromArray($paymentUnzerTransfer->toArray())
            ->setFkSalesOrder($paymentUnzerTransfer->getIdSalesOrder())
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
        SpyPaymentUnzerOrderItem      $paymentUnzerOrderItemEntity
    ): SpyPaymentUnzerOrderItem
    {
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
        SpyPaymentUnzerTransaction      $paymentUnzerTransactionEntity
    ): SpyPaymentUnzerTransaction
    {
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
        SpyPaymentUnzerTransaction      $paymentUnzerTransactionEntity,
        PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
    ): PaymentUnzerTransactionTransfer
    {
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
        UnzerCustomerTransfer   $unzerCustomerTransfer
    ): UnzerCustomerTransfer
    {
        return $unzerCustomerTransfer->setId($paymentUnzerCustomerEntity->getUnzerCustomerId());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerConfigTransfer $unzerConfigTransfer
     * @param \Orm\Zed\Unzer\Persistence\SpyUnzerConfig $unzerConfigEntity
     *
     * @return \Orm\Zed\Unzer\Persistence\SpyUnzerConfig
     */
    public function mapUnzerConfigTransferToUnzerConfigEntity(
        UnzerConfigTransfer $unzerConfigTransfer,
        SpyUnzerConfig      $unzerConfigEntity
    ): SpyUnzerConfig
    {
        return $unzerConfigEntity->fromArray($unzerConfigTransfer->toArray())
            ->setPublicKey($unzerConfigTransfer->getUnzerKeypair()->getPublicKey());
    }

    /**
     * @param \Orm\Zed\Unzer\Persistence\SpyUnzerConfig $unzerConfigEntity
     * @param \Generated\Shared\Transfer\UnzerConfigTransfer $unzerConfigTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigTransfer
     */
    public function mapUnzerConfigEntityToUnzerConfigTransfer(
        SpyUnzerConfig      $unzerConfigEntity,
        UnzerConfigTransfer $unzerConfigTransfer
    ): UnzerConfigTransfer
    {
        $unzerConfigTransfer = $unzerConfigTransfer
            ->fromArray($unzerConfigEntity->toArray(), true);

        if ($unzerConfigTransfer->getUnzerKeypair()) {
            $unzerConfigTransfer->getUnzerKeypair()
                ->setPublicKey($unzerConfigEntity->getPublicKey())
                ->setKeypairId($unzerConfigEntity->getKeypairId());

            return $unzerConfigTransfer;
        }

        return $unzerConfigTransfer->setUnzerKeypair(
            (new UnzerKeypairTransfer())
                ->setPublicKey($unzerConfigEntity->getPublicKey())
                ->setKeypairId($unzerConfigEntity->getKeypairId())
        );
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $unzerConfigStoreEntities
     * @param \Generated\Shared\Transfer\StoreRelationTransfer $storeRelationTransfer
     *
     * @return \Generated\Shared\Transfer\StoreRelationTransfer
     */
    public function mapUnzerConfigStoreEntitiesToStoreRelationTransfer(
        ObjectCollection      $unzerConfigStoreEntities,
        StoreRelationTransfer $storeRelationTransfer
    ): StoreRelationTransfer
    {
        foreach ($unzerConfigStoreEntities as $unzerConfigStoreEntity) {
            $storeRelationTransfer->addStores($this->mapStoreEntityToStoreTransfer($unzerConfigStoreEntity->getSpyStore(), new StoreTransfer()));
            $storeRelationTransfer->addIdStores($unzerConfigStoreEntity->getFkStore());
        }

        return $storeRelationTransfer;
    }

    /**
     * @param $storeEntity
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\StoreTransfer
     */
    protected function mapStoreEntityToStoreTransfer($storeEntity, StoreTransfer $storeTransfer): StoreTransfer
    {
        return $storeTransfer->fromArray($storeEntity->toArray(), true);
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $unzerConfigEntities
     * @param \Generated\Shared\Transfer\UnzerConfigCollectionTransfer $unzerConfigCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigCollectionTransfer
     */
    public function mapUnzerConfigEntityCollectionToUnzerConfigTransferCollection(
        ObjectCollection              $unzerConfigEntities,
        UnzerConfigCollectionTransfer $unzerConfigCollectionTransfer
    ): UnzerConfigCollectionTransfer
    {
        foreach ($unzerConfigEntities as $unzerConfigEntity) {
            $unzerConfigCollectionTransfer->addUnzerConfig(
                $this->mapUnzerConfigEntityToUnzerConfigTransfer($unzerConfigEntity, new UnzerConfigTransfer()),
            );
        }

        return $unzerConfigCollectionTransfer;
    }
}
