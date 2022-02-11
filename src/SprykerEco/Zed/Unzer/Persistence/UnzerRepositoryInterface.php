<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Persistence;

use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCriteriaTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;

interface UnzerRepositoryInterface
{
    /**
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer|null
     */
    public function findPaymentUnzerByOrderReference(string $orderReference): ?PaymentUnzerTransfer;

    /**
     * @param string $orderId
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer
     */
    public function getPaymentUnzerOrderItemCollectionByOrderId(string $orderId): PaymentUnzerOrderItemCollectionTransfer;

    /**
     * @param string $paymentId
     * @param string $unzerKeypairId
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer|null
     */
    public function findPaymentUnzerByPaymentIdAndKeypairId(string $paymentId, string $unzerKeypairId): ?PaymentUnzerTransfer;

    /**
     * @param int $idSalesOrderItem
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer|null
     */
    public function findPaymentUnzerOrderItemByIdSalesOrderItem(int $idSalesOrderItem): ?PaymentUnzerOrderItemTransfer;

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
    ): ?PaymentUnzerTransactionTransfer;

    /**
     * @param int $idCustomer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer|null
     */
    public function findUnzerCustomerByIdCustomer(int $idCustomer): ?UnzerCustomerTransfer;

    /**
     * @param int $idUnzerCredentials
     *
     * @return \Generated\Shared\Transfer\StoreRelationTransfer
     */
    public function getStoreRelationByIdUnzerCredentials(int $idUnzerCredentials): StoreRelationTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer
     */
    public function findUnzerCredentialsCollectionByCriteria(
        UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer
    ): UnzerCredentialsCollectionTransfer;

    /**
     * @param PaymentUnzerTransactionCriteriaTransfer $paymentUnzerTransactionCriteriaTransfer
     *
     * @return PaymentUnzerTransactionCollectionTransfer
     */
    public function findPaymentUnzerTransactionCollectionByCriteria(
        PaymentUnzerTransactionCriteriaTransfer $paymentUnzerTransactionCriteriaTransfer
    ): PaymentUnzerTransactionCollectionTransfer;
}
