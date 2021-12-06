<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Reader;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerConfigCollectionTransfer;
use Generated\Shared\Transfer\UnzerConfigCriteriaTransfer;
use Generated\Shared\Transfer\UnzerConfigTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;

interface UnzerReaderInterface
{
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
     * @param string $publicKey
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer|null
     */
    public function getPaymentUnzerByPaymentIdAndPublicKey(string $unzerPaymentId, string $publicKey): ?PaymentUnzerTransfer;

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
     * @param \Generated\Shared\Transfer\UnzerConfigCriteriaTransfer $unzerConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigTransfer|null
     */
    public function getUnzerConfigByCriteria(UnzerConfigCriteriaTransfer $unzerConfigCriteriaTransfer): ?UnzerConfigTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerConfigCriteriaTransfer $unzerConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigCollectionTransfer
     */
    public function getUnzerConfigCollectionByCriteria(UnzerConfigCriteriaTransfer $unzerConfigCriteriaTransfer): UnzerConfigCollectionTransfer;
}
