<?php

namespace SprykerEcoTest\Zed\Unzer\Business;

use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;

class SetUnzerNotificationUrlFacadeTest extends UnzerFacadeBaseTest
{
    public function testSetUnzerNotificationUrlSuccess(): void
    {
        //Arrange
        $unzerNotificationConfigTransfer = $this->tester->createUnzerNotificationConfigTransfer();

        //Act
        $this->facade->setUnzerNotificationUrl($unzerNotificationConfigTransfer);

        //Assert
        $this->assertTrue(true);
    }


    public function testSetUnzerNotificationUrlThrowsException(): void
    {
        //Arrange
        $unzerNotificationConfigTransfer = $this->tester->createUnzerNotificationConfigTransfer();
        $unzerNotificationConfigTransfer->setUnzerKeyPair(null);

        //Assert
        $this->expectException(UnzerException::class);

        //Act
        $this->facade->setUnzerNotificationUrl($unzerNotificationConfigTransfer);
    }
}
