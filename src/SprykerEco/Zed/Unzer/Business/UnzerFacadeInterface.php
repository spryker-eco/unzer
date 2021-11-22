<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantCollectionTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantCriteriaTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use Generated\Shared\Transfer\UnzerNotificationConfigTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;

interface UnzerFacadeInterface
{
    /**
     * Specification:
     *  - Performs Unzer Create Customer API call.
     *  - Saves Unzer Customer to Quote.
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
     * - Saves order payment method data according to quote and checkout response transfer data.
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
     *  - Performs Unzer Create Basket API call.
     *  - Performs Unzer Create payment resource API call.
     *  - Performs Unzer Authorize or Change API call.
     *  - Saves payment detailed info to persistence.
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
     *  - Filters available payment methods.
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
     *  - Get Merchant Unzer participants collection by criteria.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantUnzerParticipantCriteriaTransfer $merchantUnzerParticipantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUnzerParticipantCollectionTransfer
     */
    public function getMerchantUnzerParticipantCollection(
        MerchantUnzerParticipantCriteriaTransfer $merchantUnzerParticipantCriteriaTransfer
    ): MerchantUnzerParticipantCollectionTransfer;

    /**
     * Specification:
     *  - Saves Unzer Participant Id for Merchant to DB.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function saveMerchantUnzerParticipantByMerchant(MerchantTransfer $merchantTransfer): MerchantResponseTransfer;

    /**
     * Specification:
     *  - Performs Unzer Set Notification URL Api all.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerApiRequestTransfer $unzerApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiResponseTransfer
     */
    public function performSetNotificationUrlApiCall(UnzerApiRequestTransfer $unzerApiRequestTransfer): UnzerApiResponseTransfer;

    /**
     * Specification:
     *  - Prepares UnzerApi request and set Unzer keypair.
     *  - Performs Unzer Set Notification URL Api all.
     *  - Throws UnzerException if API call failed.
     *
     * @param UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer
     *
     * @return void
     *
     * @throws UnzerException
     */
    public function setUnzerNotificationUrl(UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer): void;

}
