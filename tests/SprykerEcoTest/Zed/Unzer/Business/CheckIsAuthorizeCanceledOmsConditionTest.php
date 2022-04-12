<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\DataBuilder\SaveOrderBuilder;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use SprykerEco\Zed\Unzer\UnzerConstants;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group Facade
 * @group CheckIsAuthorizeCanceledOmsConditionTest
 */
class CheckIsAuthorizeCanceledOmsConditionTest extends UnzerFacadeBaseTest
{
    /**
     * @var string
     */
    public const STATE_MACHINE_PROCESS_NAME = 'UnzerSofort01';

    /**
     * @return void
     */
    public function testCheckIsAuthorizeCanceledOmsConditionReturnsTrueWhileOmsStatusIsAuthorizeCanceled(): void
    {
        //Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials();
        $saveOrderTransfer = $this->tester->haveOrder(
            (new SaveOrderBuilder())
                ->withOrderItem()
                ->build()
                ->toArray(),
            static::STATE_MACHINE_PROCESS_NAME,
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
            PaymentUnzerOrderItemTransfer::STATUS => UnzerConstants::OMS_STATUS_AUTHORIZE_CANCELED,
        ]);

        //Act
        $isAuthorizeCanceledOmsCondition = $this->tester
            ->getFacade()
            ->checkIsAuthorizeCanceledOmsCondition($paymentUnzerOrderItemTransfer->getIdSalesOrderItem());

        //Assert
        $this->assertTrue($isAuthorizeCanceledOmsCondition);
    }

    /**
     * @return void
     */
    public function testCheckIsAuthorizeCanceledOmsConditionReturnsFalseWhileOmsStatusIsNotAuthorizeCanceled(): void
    {
        //Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials();
        $saveOrderTransfer = $this->tester->haveOrder(
            (new SaveOrderBuilder())
                ->withOrderItem()
                ->build()
                ->toArray(),
            static::STATE_MACHINE_PROCESS_NAME,
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

        //Act
        $isAuthorizeCanceledOmsCondition = $this->tester
            ->getFacade()
            ->checkIsAuthorizeCanceledOmsCondition($paymentUnzerOrderItemTransfer->getIdSalesOrderItem());

        //Assert
        $this->assertFalse($isAuthorizeCanceledOmsCondition);
    }
}
