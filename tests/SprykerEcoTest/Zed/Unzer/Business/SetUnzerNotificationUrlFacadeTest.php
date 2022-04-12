<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group UnzerFacade
 * @group SetUnzerNotificationUrlFacadeTest
 */
class SetUnzerNotificationUrlFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testSetUnzerNotificationUrl(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials();

        //Act
        $this->tester->getFacade()->setUnzerNotificationUrl($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue(true);
    }
}
