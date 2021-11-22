<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Reader;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantCollectionTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;

interface UnzerReaderInterface
{
    /**
     * @param \Generated\Shared\Transfer\MerchantUnzerParticipantCriteriaTransfer $merchantUnzerParticipantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer|null
     */
    public function getMerchantUnzerParticipantByCriteria(
        MerchantUnzerParticipantCriteriaTransfer $merchantUnzerParticipantCriteriaTransfer
    ): ?MerchantUnzerParticipantTransfer;

    /**
     * @param \Generated\Shared\Transfer\MerchantUnzerParticipantCriteriaTransfer $merchantUnzerParticipantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUnzerParticipantCollectionTransfer
     */
    public function getMerchantUnzerParticipantCollectionByCriteria(
        MerchantUnzerParticipantCriteriaTransfer $merchantUnzerParticipantCriteriaTransfer
    ): MerchantUnzerParticipantCollectionTransfer;

    /**
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    public function getPaymentUnzerByOrderReference(string $orderReference): PaymentUnzerTransfer;

    /**
     * @param string $orderId
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer
     */
    public function getPaymentUnzerOrderItemCollectionByOrderId(string $orderId): PaymentUnzerOrderItemCollectionTransfer;

    /**
     * @param string $unzerPaymentId
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    public function getPaymentUnzerByPaymentId(string $unzerPaymentId): PaymentUnzerTransfer;

    /**
     * @param int $idSalesOrderItem
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer
     */
    public function getPaymentUnzerOrderItemByIdSalesOrderItem(int $idSalesOrderItem): PaymentUnzerOrderItemTransfer;

    /**
     * @param string $paymentId
     * @param string $transactionType
     * @param string|null $participantId
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer
     */
    public function getPaymentUnzerTransactionByPaymentIdAndParticipantId(
        string $paymentId,
        string $transactionType,
        ?string $participantId = null
    ): PaymentUnzerTransactionTransfer;

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer|null
     */
    public function getUnzerCustomerTransferByCustomerTransfer(CustomerTransfer $customerTransfer): ?UnzerCustomerTransfer;

    /**
     * @param string $merchantReference
     * @param StoreTransfer $storeTransfer
     * @return UnzerKeypairTransfer|null
     */
    public function getUnzerKeypairByMerchantReferenceAndStoreId(string $merchantReference, StoreTransfer $storeTransfer): ? UnzerKeypairTransfer;

    /**
     * @param string $unzerPrimaryKeypairId
     *
     * @return UnzerKeypairTransfer|null
     */
    public function getUnzerKeypairByKeypairId(string $unzerPrimaryKeypairId): ?UnzerKeypairTransfer;
}
