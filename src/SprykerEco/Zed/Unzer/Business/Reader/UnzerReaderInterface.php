<?php

namespace SprykerEco\Zed\Unzer\Business\Reader;

use Generated\Shared\Transfer\MerchantUnzerParticipantTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;

interface UnzerReaderInterface
{
    /**
     * @param string $merchantReference
     *
     * @return \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer
     */
    public function getMerchantUnzerByMerchantReference(string $merchantReference): MerchantUnzerParticipantTransfer;

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
    public function getPaymentUnzerOrderItemCollectionByOrderReference(string $orderId): PaymentUnzerOrderItemCollectionTransfer;

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
}
