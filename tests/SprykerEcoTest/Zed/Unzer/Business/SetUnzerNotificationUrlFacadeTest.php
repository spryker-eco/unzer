<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

class SetUnzerNotificationUrlFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testSetUnzerNotificationUrl(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials($this->tester->haveStore());

        //Act
        $this->facade->setUnzerNotificationUrl($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue(true);
    }
}
