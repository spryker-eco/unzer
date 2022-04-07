<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\Transfer\UnzerCredentialsTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group Facade
 * @group DeleteUnzerCredentialsFacadeTest
 */
class DeleteUnzerCredentialsFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testDeleteUnzerCredentialsWithChildrenFailed(): void
    {
        //Arrange
        $marketplaceUnzerCredentials = $this->tester->haveMarketplaceUnzerCredentials();
        $this->tester->haveMarketplaceMerchantUnzerCredentials([
            UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => $marketplaceUnzerCredentials->getIdUnzerCredentials(),
            UnzerCredentialsTransfer::MERCHANT_REFERENCE => '',
        ]);

        //Act
        $unzerCredentialsResponseTransfer = $this->tester->getFacade()->deleteUnzerCredentials($marketplaceUnzerCredentials);

        //Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testDeleteMarketplaceUnzerCredentialsSuccess(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->haveMarketplaceUnzerCredentials();

        //Act
        $unzerCredentialsResponseTransfer = $this->tester->getFacade()->deleteUnzerCredentials($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testDeleteStandardUnzerCredentialsSuccess(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials();

        //Act
        $unzerCredentialsResponseTransfer = $this->tester->getFacade()->deleteUnzerCredentials($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
    }
}
