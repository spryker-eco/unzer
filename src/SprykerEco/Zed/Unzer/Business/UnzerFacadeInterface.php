<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;

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
     * @param int[] $salesOrderItemIds
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
     * @param int[] $salesOrderItemIds
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
     *  - Get unzer merchant information by merchant reference.
     *
     * @api
     *
     * @param string $merchantReference
     *
     * @return \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer
     */
    public function getUnzerMerchantByMerchantReference(string $merchantReference): MerchantUnzerParticipantTransfer;

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
}
