<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Mapper;

use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Generated\Shared\Transfer\UnzerTransactionTransfer;

interface UnzerPaymentMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    public function mapUnzerPaymentTransferToPaymentUnzerTransfer(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer
    ): PaymentUnzerTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapPaymentUnzerTransferToUnzerPaymentTransfer(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerTransactionTransfer $unzerTransactionTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer
     */
    public function mapUnzerTransactionTransferToPaymentUnzerTransactionTransfer(
        UnzerTransactionTransfer $unzerTransactionTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer,
        PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
    ): PaymentUnzerTransactionTransfer;
}
