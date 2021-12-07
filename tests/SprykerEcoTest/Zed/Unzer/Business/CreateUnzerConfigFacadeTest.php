<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

class CreateUnzerCredentialsFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testCreateUnzerCredentials(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->createUnzerCredentialsTransfer();

        //Act
        $unzerCredentialsResponseTransfer = $this->facade->createUnzerCredentials($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
    }
}
