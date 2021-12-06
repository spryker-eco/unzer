<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;

class SetUnzerNotificationUrlFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testSetUnzerNotificationUrlSuccess(): void
    {
        //Arrange
        $unzerNotificationConfigTransfer = $this->tester->createUnzerNotificationConfigTransfer();

        //Act
        $this->facade->setUnzerNotificationUrl($unzerNotificationConfigTransfer);

        //Assert
        $this->assertTrue(true);
    }

    /**
     * @return void
     */
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
