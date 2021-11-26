<?php

namespace SprykerEcoTest\Zed\Unzer\Business;

class ProcessNotificationFacadeTest extends UnzerFacadeBaseTest
{

    public function testProcessNotificationSuccessful(): void
    {
        //Arrange
        $unzerNotificationTransfer = $this->tester->createUnzerNotificationTransfer();
//        $this->tester->haveUnzerPayment();

        //Act
        $unzerNotificationTransfer = $this->facade->processNotification($unzerNotificationTransfer);

        //Assert
        $this->assertTrue($unzerNotificationTransfer->getIsProcessed());
    }


    public function testProcessNotificationSkip(): void
    {
        //Arrange
        $unzerNotificationTransfer = $this->tester->createUnzerNotificationTransfer();
        $unzerNotificationTransfer->setEvent('disabled.event');

        //Act
        $unzerNotificationTransfer = $this->facade->processNotification($unzerNotificationTransfer);

        //Assert
        $this->assertTrue($unzerNotificationTransfer->getIsProcessed());
    }

    public function testProcessNotificationTooEarly(): void
    {
        //Arrange
        $unzerNotificationTransfer = $this->tester->createUnzerNotificationTransfer();

        //Act
        $unzerNotificationTransfer = $this->facade->processNotification($unzerNotificationTransfer);

        //Assert
        $this->assertFalse($unzerNotificationTransfer->getIsProcessed());
    }
}
