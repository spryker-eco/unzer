<?php

namespace SprykerEco\Zed\Unzer\Persistence;

use Generated\Shared\Transfer\MerchantUnzerParticipantTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;

interface UnzerEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    public function savePaymentUnzerEntity(PaymentUnzerTransfer $paymentUnzerTransfer): PaymentUnzerTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer
     */
    public function savePaymentUnzerOrderItemEntity(
        PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
    ): PaymentUnzerOrderItemTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer
     */
    public function savePaymentUnzerTransactionEntity(
        PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
    ): PaymentUnzerTransactionTransfer;

    /**
     * @param \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer $merchantUnzerParticipantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer
     */
    public function saveMerchantUnzerParticipantEntity(MerchantUnzerParticipantTransfer $merchantUnzerParticipantTransfer): MerchantUnzerParticipantTransfer;
}
