<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business\UnzerFacade;

use Generated\Shared\Transfer\SaveOrderTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEcoTest\Zed\Unzer\Business\UnzerFacadeBaseTest;
use SprykerEcoTest\Zed\Unzer\UnzerBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group UnzerFacade
 * @group SaveOrderPaymentTest
 */
class SaveOrderPaymentTest extends UnzerFacadeBaseTest
{
    /**
     * @var string
     */
    protected const OMS_PROCESS = 'Test01';

    /**
     * @return void
     */
    public function testSaveOrderPaymentSuccess(): void
    {
        //Arrange
        $unzerPaymentTransfer = $this->tester->createUnzerPaymentTransfer(false, false);
        $paymentTransfer = $this->tester->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_SOFORT)->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester->createQuoteTransfer()->setPayment($paymentTransfer);
        $this->tester->configureTestStateMachine([static::OMS_PROCESS]);
        $saveOrderTransfer = $this->tester->haveOrder([
            SaveOrderTransfer::ORDER_REFERENCE => UnzerBusinessTester::ORDER_REFERENCE,
        ], static::OMS_PROCESS);

        //Act
        $this->tester->getFacade()->saveOrderPayment($quoteTransfer, $saveOrderTransfer);

        //Assert
        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testSaveOrderPaymentShouldThrowAnExceptionWhenOrderAlreadyExists(): void
    {
        //Arrange
        $unzerPaymentTransfer = $this->tester->createUnzerPaymentTransfer(false, false);
        $paymentTransfer = $this->tester->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_SOFORT)->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester->createQuoteTransfer()->setPayment($paymentTransfer);
        $this->tester->configureTestStateMachine([static::OMS_PROCESS]);
        $saveOrderTransfer = $this->tester->haveOrder([
            SaveOrderTransfer::ORDER_REFERENCE => UnzerBusinessTester::ORDER_REFERENCE,
        ], static::OMS_PROCESS);

        //Act
        $this->tester->getFacade()->saveOrderPayment($quoteTransfer, $saveOrderTransfer);

        //Assert
        $this->expectException(UnzerException::class);

        //Act
        $this->tester->getFacade()->saveOrderPayment($quoteTransfer, $saveOrderTransfer);
    }
}
