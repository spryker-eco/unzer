<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\UnzerConfigResponseTransfer;
use Generated\Shared\Transfer\UnzerConfigTransfer;
use Generated\Shared\Transfer\UnzerNotificationConfigTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;

interface UnzerFacadeInterface
{
    /**
     * Specification:
     *  - Expands QuoteTransfer with UnzerPaymentTransfer.
     *  - Expands QuoteTransfer with UnzerKeypairTransfer.
     *  - Expands QuoteTransfer with UnzerCustomerTransfer.
     *  - Expands QuoteTransfer with UnzerMetadataTransfer.
     *  - If QuoteTransfer contains marketplace items - expands ItemTransfers with Unzer Participant ID.
     *  - Performs Unzer Create Customer API call.
     *  - Performs Unzer Update Customer API call.
     *  - Performs Unzer Create Metadata API call.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function performPreSaveOrderStack(QuoteTransfer $quoteTransfer): QuoteTransfer;

    /**
     * Specification:
     * - Saves Unzer payment details to Persistence.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void;

    /**
     * Specification:
     *  - Checks if provided Unzer notification is enabled.
     *  - Processes Unzer notification.
     *  - Updates payment details in DB.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerNotificationTransfer $notificationTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerNotificationTransfer
     */
    public function processNotification(UnzerNotificationTransfer $notificationTransfer): UnzerNotificationTransfer;

    /**
     * Specification:
     *  - Expands QuoteTransfer with UnzerBasketTransfer.
     *  - Expands QuoteTransfer with UnzerPaymentResourceTransfer.
     *  - Performs Unzer Create Basket API call.
     *  - Performs Unzer Create payment resource API call.
     *  - Performs Unzer Authorize or Change API call depending on payment type.
     *  - Saves payment detailed info to Persistence.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     *
     * @return void
     */
    public function executePostSaveHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse): void;

    /**
     * Specification:
     *  - Checks if Unzer Authorization is pending.
     *
     * @api
     *
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function checkIsAuthorizePendingOmsCondition(int $idSalesOrderItem): bool;

    /**
     * Specification:
     *  - Checks if Unzer Authorization is success.
     *
     * @api
     *
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function checkIsAuthorizeSucceededOmsCondition(int $idSalesOrderItem): bool;

    /**
     * Specification:
     *  - Checks if Unzer Authorization is failed.
     *
     * @api
     *
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function checkIsAuthorizeFailedOmsCondition(int $idSalesOrderItem): bool;

    /**
     * Specification:
     *  - Checks if Unzer Authorization is canceled.
     *
     * @api
     *
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function checkIsAuthorizeCanceledOmsCondition(int $idSalesOrderItem): bool;

    /**
     * Specification:
     *  - Checks if Unzer Payment is completed.
     *
     * @api
     *
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function checkIsPaymentCompletedOmsCondition(int $idSalesOrderItem): bool;

    /**
     * Specification:
     *  - Checks if Unzer Charge failed.
     *
     * @api
     *
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function checkIsChargeFailedOmsCondition(int $idSalesOrderItem): bool;

    /**
     * Specification:
     *  - Checks if Unzer Payment is charged-back.
     *
     * @api
     *
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function checkIsPaymentChargebackOmsCondition(int $idSalesOrderItem): bool;

    /**
     * Specification:
     *  - Executes Unzer API Charge request.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return void
     */
    public function executeChargeOmsCommand(OrderTransfer $orderTransfer, array $salesOrderItemIds): void;

    /**
     * Specification:
     *  - Executes Unzer API Refund request.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return void
     */
    public function executeRefundOmsCommand(
        RefundTransfer $refundTransfer,
        OrderTransfer $orderTransfer,
        array $salesOrderItemIds
    ): void;

    /**
     * Specification:
     *  - Filters available marketplace payment methods based on quote items.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function filterMarketplacePaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        QuoteTransfer $quoteTransfer
    ): PaymentMethodsTransfer;

    /**
     * Specification:
     *  - Prepares UnzerApi request and set Unzer keypair.
     *  - Performs Unzer Set Notification URL Api all.
     *  - Throws UnzerException if API call failed.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return void
     */
    public function setUnzerNotificationUrl(UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer): void;

    /**
     * Specification:
     *  - Saves UnzerConfigTransfer to Persistence.
     *  - If UnzerConfigTransfer contains store relations - also saves it to Persistence.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerConfigTransfer $unzerConfigTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigResponseTransfer
     */
    public function createUnzerConfig(UnzerConfigTransfer $unzerConfigTransfer): UnzerConfigResponseTransfer;

    /**
     * Specification:
     *  - Updates UnzerConfigTransfer to Persistence.
     *  - If UnzerConfigTransfer contains store relations - also updates it to Persistence.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerConfigTransfer $unzerConfigTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigResponseTransfer
     */
    public function updateUnzerConfig(UnzerConfigTransfer $unzerConfigTransfer): UnzerConfigResponseTransfer;
}
