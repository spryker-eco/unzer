<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Persistence;

use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerConfigTransfer;

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
     * @param \Generated\Shared\Transfer\UnzerConfigTransfer $unzerConfigTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigTransfer
     */
    public function createUnzerConfig(UnzerConfigTransfer $unzerConfigTransfer): UnzerConfigTransfer;

    /**
     * @param array $idStores
     * @param int $idUnzerConfig
     *
     * @return void
     */
    public function addUnzerConfigStoreRelationsForStores(array $idStores, int $idUnzerConfig): void;

    /**
     * @param array $idStores
     * @param int $idUnzerConfig
     *
     * @return void
     */
    public function removeUnzerConfigStoreRelationsForStores(array $idStores, int $idUnzerConfig): void;

    /**
     * @param \Generated\Shared\Transfer\UnzerConfigTransfer $unzerConfigTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigTransfer|null
     */
    public function updateUnzerConfig(UnzerConfigTransfer $unzerConfigTransfer): ?UnzerConfigTransfer;
}
