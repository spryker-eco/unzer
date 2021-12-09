<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use SprykerEco\Shared\Unzer\UnzerConstants;

class SetUnzerNotificationUrlFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testSetUnzerNotificationUrl(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->createUnzerCredentialsTransfer(UnzerConstants::UNZER_CONFIG_TYPE_STANDARD);

        //Act
        $this->facade->setUnzerNotificationUrl($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue(true);
    }
}
