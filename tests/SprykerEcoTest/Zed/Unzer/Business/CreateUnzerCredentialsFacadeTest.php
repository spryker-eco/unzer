<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use SprykerEco\Shared\Unzer\UnzerConstants;

class CreateUnzerCredentialsFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testCreateUnzerCredentials(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->createUnzerCredentialsTransfer(UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE);

        //Act
        $unzerCredentialsResponseTransfer = $this->facade->createUnzerCredentials($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
    }
}
