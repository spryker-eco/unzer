<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

class DeleteUnzerCredentialsFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testDeleteUnzerCredentialsWithChildrenFailed(): void
    {
        //Arrange
        $parentUnzerCredentialsTransfer = $this->tester->haveMarketplaceUnzerCredentials($this->tester->haveStore());
        $this->tester->haveMerchantUnzerCredentials($parentUnzerCredentialsTransfer, '');

        //Act
        $unzerCredentialsResponseTransfer = $this->facade->deleteUnzerCredentials($parentUnzerCredentialsTransfer);

        //Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testDeleteMarketplaceUnzerCredentialsSuccess(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->haveMarketplaceUnzerCredentials($this->tester->haveStore());

        //Act
        $unzerCredentialsResponseTransfer = $this->facade->deleteUnzerCredentials($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testDeleteStandardUnzerCredentialsSuccess(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials($this->tester->haveStore());

        //Act
        $unzerCredentialsResponseTransfer = $this->facade->deleteUnzerCredentials($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
    }
}
