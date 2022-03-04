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
use Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;

interface UnzerFacadeInterface
{
    /**
     * Specification:
     * - Requires `QuoteTransfer.payment.unzerPayment` to be set.
     * - Requires `QuoteTransfer.customer` to be set.
     * - Requires `QuoteTransfer.store` to be set.
     * - Expands `QuoteTransfer` with `UnzerPaymentTransfer`.
     * - Expands `QuoteTransfer` with `UnzerKeypairTransfer`.
     * - Expands `QuoteTransfer` with `UnzerCustomerTransfer`.
     * - Expands `QuoteTransfer` with `UnzerMetadataTransfer`.
     * - If `QuoteTransfer` contains marketplace items - expands `QuoteTransfer.items` with Unzer Participant ID.
     * - Performs Unzer Create Customer API call.
     * - Performs Unzer Update Customer API call.
     * - Performs Unzer Create Metadata API call.
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
     * - Requires `SaveOrderTransfer.IdSalesOrder` to be set.
     * - Requires `QuoteTransfer.payment.unzerPayment.customer` to be set.
     * - Requires `QuoteTransfer.payment.unzerPayment.unzerKeypair.keypairId` to be set.
     * - Requires `SaveOrderTransfer.orderReference` to bet set.
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
     * - Checks if provided Unzer notification is enabled.
     * - Processes Unzer notification.
     * - Updates payment details in DB.
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
     * - Requires `QuoteTransfer.payment.unzerPayment.idSalesOrder` to be set.
     * - Expands `QuoteTransfer` with `UnzerBasketTransfer`.
     * - Expands `QuoteTransfer` with `UnzerPaymentResourceTransfer`.
     * - Performs Unzer Create Basket API call.
     * - Performs Unzer Create payment resource API call.
     * - Performs Unzer Authorize or Change API call depending on payment type.
     * - Saves payment detailed info to Persistence.
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
     * - Checks if Unzer Authorization is pending.
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
     * - Checks if Unzer Authorization is successful.
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
     * - Checks if Unzer Authorization is failed.
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
     * - Checks if Unzer Authorization is canceled.
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
     * - Checks if Unzer Payment is completed.
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
     * - Checks if Unzer Charge failed.
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
     * - Checks if Unzer Payment is charged-back.
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
     * - Executes Unzer API Charge request.
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
     * - Requires `OrderTransfer.payments.paymentProvider.paymentMethod` to be set.
     * - Executes Unzer API Refund request.
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
     * - Filters available marketplace payment methods based on quote items.
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
     * - Filters available payment methods and checks intersections based on merchants.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function filterIntersectionPaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        QuoteTransfer $quoteTransfer
    ): PaymentMethodsTransfer;

    /**
     * Specification:
     * - Requires `UnzerCredentialsTransfer.unzerKeypair` to be set.
     * - Prepares UnzerApi request and set Unzer keypair.
     * - Performs Unzer Set Notification URL Api call.
     * - Throws `UnzerException` if API call failed.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return void
     */
    public function setUnzerNotificationUrl(UnzerCredentialsTransfer $unzerCredentialsTransfer): void;

    /**
     * Specification:
     * - Saves `UnzerCredentialsTransfer` to persistence.
     * - Saves `UnzerCredentialsTransfer.storeRelation` to persistence if defined.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function createUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer;

    /**
     * Specification:
     * - Requires `UnzerCredentialsTransfer.unzerKeypair.publicKey` to be set.
     * - Requires `UnzerCredentialsTransfer.idUnzerCredentials` to be set.
     * - Updates `UnzerCredentialsTransfer` to Persistence.
     * - If `UnzerCredentialsTransfer` contains store relations - also updates it to Persistence.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function updateUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer;

    /**
     * Specification:
     * - Imports available Unzer payment methods and saves them in persistence.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return void
     */
    public function performPaymentMethodsImport(UnzerKeypairTransfer $unzerKeypairTransfer): void;

    /**
     * Specification:
     * - Fetches `UnzerCredentialsCollectionTransfer` by given criteria from Persistence.
     * - Expands each `UnzerCredentialsTransfer` with `UnzerKeypairTransfer`.
     * - Builds `UnzerKeypairTransfer` using private key from `Vault`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer
     */
    public function getUnzerCredentialsCollection(
        UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer
    ): UnzerCredentialsCollectionTransfer;

    /**
     * Specification:
     * - Requires `UnzerCredentialsTransfer.unzerKeypair` to be set.
     * - Requires `UnzerCredentialsTransfer.idUnzerCredentials` to be set.
     * - Checks ability to delete entity from Persistence.
     * - Deletes `UnzerCredentialsTransfer` from Persistence.
     * - If `UnzerCredentialsTransfer` contains store relations - also deletes it from Persistence.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function deleteUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer;

    /**
     * Specification:
     * - Validates `UnzerCredentialsTransfer` properties using internal rules.
     * - Returns validation errors if validation fails.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function validateUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer;
}
