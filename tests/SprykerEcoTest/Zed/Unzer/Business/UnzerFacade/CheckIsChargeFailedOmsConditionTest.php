<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business\UnzerFacade;

use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use SprykerEco\Zed\Unzer\UnzerConstants;
use SprykerEcoTest\Zed\Unzer\Business\UnzerFacadeBaseTest;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group UnzerFacade
 */
class CheckIsChargeFailedOmsConditionTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testCheckIsChargeFailedOmsConditionReturnsTrueWhileOmsStatusIsChargeFailed(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials();
        $saveOrderTransfer = $this->tester->haveOrderUsingPreparedQuoteTransfer(
            $this->tester->createQuoteTransfer(),
            static::UNZER_SOFORT_STATE_MACHINE_PROCESS_NAME,
        );

        $paymentUnzerTransfer = $this->tester->havePaymentUnzer([
            PaymentUnzerTransfer::ID_SALES_ORDER => $saveOrderTransfer->getIdSalesOrder(),
            PaymentUnzerTransfer::KEYPAIR_ID => $unzerCredentialsTransfer->getKeypairId(),
            PaymentUnzerTransfer::CUSTOMER_ID => $customerTransfer->getIdCustomer(),
            PaymentUnzerTransfer::ORDER_ID => $saveOrderTransfer->getOrderReference(),
        ]);

        $itemTransfer = $saveOrderTransfer->getOrderItems()->offsetGet(0);

        $paymentUnzerOrderItemTransfer = $this->tester->havePaymentUnzerOrderItem([
            PaymentUnzerOrderItemTransfer::ID_SALES_ORDER_ITEM => $itemTransfer->getIdSalesOrderItem(),
            PaymentUnzerOrderItemTransfer::ID_PAYMENT_UNZER => $paymentUnzerTransfer->getIdPaymentUnzer(),
            PaymentUnzerOrderItemTransfer::STATUS => UnzerConstants::OMS_STATUS_CHARGE_FAILED,
        ]);

        // Act
        $isChargeFailedOmsConditionOmsCondition = $this->tester
            ->getFacade()
            ->checkIsChargeFailedOmsCondition($paymentUnzerOrderItemTransfer->getIdSalesOrderItem());

        // Assert
        $this->assertTrue($isChargeFailedOmsConditionOmsCondition);
    }

    /**
     * @return void
     */
    public function testCheckIsChargeFailedOmsConditionReturnsFalseWhileOmsStatusIsNotChargedFailed(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials();
        $saveOrderTransfer = $this->tester->haveOrderUsingPreparedQuoteTransfer(
            $this->tester->createQuoteTransfer(),
            static::UNZER_SOFORT_STATE_MACHINE_PROCESS_NAME,
        );

        $paymentUnzerTransfer = $this->tester->havePaymentUnzer([
            PaymentUnzerTransfer::ID_SALES_ORDER => $saveOrderTransfer->getIdSalesOrder(),
            PaymentUnzerTransfer::KEYPAIR_ID => $unzerCredentialsTransfer->getKeypairId(),
            PaymentUnzerTransfer::CUSTOMER_ID => $customerTransfer->getIdCustomer(),
            PaymentUnzerTransfer::ORDER_ID => $saveOrderTransfer->getOrderReference(),

        ]);

        $itemTransfer = $saveOrderTransfer->getOrderItems()->offsetGet(0);

        $paymentUnzerOrderItemTransfer = $this->tester->havePaymentUnzerOrderItem([
            PaymentUnzerOrderItemTransfer::ID_SALES_ORDER_ITEM => $itemTransfer->getIdSalesOrderItem(),
            PaymentUnzerOrderItemTransfer::ID_PAYMENT_UNZER => $paymentUnzerTransfer->getIdPaymentUnzer(),
            PaymentUnzerOrderItemTransfer::STATUS => UnzerConstants::OMS_STATUS_NEW,
        ]);

        // Act
        $isChargeFailedOmsConditionOmsCondition = $this->tester
            ->getFacade()
            ->checkIsChargeFailedOmsCondition($paymentUnzerOrderItemTransfer->getIdSalesOrderItem());

        // Assert
        $this->assertFalse($isChargeFailedOmsConditionOmsCondition);
    }
}
