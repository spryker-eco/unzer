<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEcoTest\Zed\Unzer\UnzerZedTester;

class SaveOrderPaymentFacadeTest extends UnzerFacadeBaseTest
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
        $quoteTransfer = $this->tester->createQuoteTransfer();
        $this->tester->configureTestStateMachine([static::OMS_PROCESS]);
        $saveOrderTransfer = $this->tester->haveOrder([
            'orderReference' => UnzerZedTester::ORDER_REFERENCE,
        ], static::OMS_PROCESS);

        //Act
        $this->facade->saveOrderPayment($quoteTransfer, $saveOrderTransfer);

        //Assert
        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testSaveOrderPaymentShouldThrowAnExceptionWhenOrderAlreadyExists(): void
    {
        //Arrange
        $quoteTransfer = $this->tester->createQuoteTransfer();
        $this->tester->configureTestStateMachine([static::OMS_PROCESS]);
        $saveOrderTransfer = $this->tester->haveOrder([
            'orderReference' => UnzerZedTester::ORDER_REFERENCE,
        ], static::OMS_PROCESS);

        //Act
        $this->facade->saveOrderPayment($quoteTransfer, $saveOrderTransfer);

        //Assert
        $this->expectException(UnzerException::class);

        //Act
        $this->facade->saveOrderPayment($quoteTransfer, $saveOrderTransfer);
    }
}
