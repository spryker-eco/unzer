<?php

namespace SprykerEcoTest\Zed\Unzer\Business;

use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEcoTest\Zed\Unzer\UnzerZedTester;

class SaveOrderPaymentFacadeTest extends UnzerFacadeBaseTest
{
    protected const OMS_PROCESS = 'Test01';

    public function testSaveOrderPaymentSuccess()
    {
        //Arrange
        $quoteTransfer = $this->tester->createQuoteTransfer();
        $this->tester->configureTestStateMachine([static::OMS_PROCESS]);
        $saveOrderTransfer = $this->tester->haveOrder([
            'orderReference' => UnzerZedTester::ORDER_REFERENCE
        ], static::OMS_PROCESS);

        //Act
        $this->facade->saveOrderPayment($quoteTransfer, $saveOrderTransfer);

        //Assert
        $this->assertTrue(true);
    }

    public function testSaveOrderPaymentFail()
    {
        //Arrange
        $quoteTransfer = $this->tester->createQuoteTransfer();
        $this->tester->configureTestStateMachine([static::OMS_PROCESS]);
        $saveOrderTransfer = $this->tester->haveOrder([
            'orderReference' => UnzerZedTester::ORDER_REFERENCE
        ], static::OMS_PROCESS);

        //Act
        $this->facade->saveOrderPayment($quoteTransfer, $saveOrderTransfer);

        //Assert
        $this->expectException(UnzerException::class);

        //Act
        $this->facade->saveOrderPayment($quoteTransfer, $saveOrderTransfer);
    }
}
