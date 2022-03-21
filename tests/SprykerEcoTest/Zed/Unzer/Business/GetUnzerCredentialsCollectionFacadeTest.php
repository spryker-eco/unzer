<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;

class GetUnzerCredentialsCollectionFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testGetUnzerCredentialsCollectionExists(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->haveMarketplaceUnzerCredentials($this->tester->haveStore());

        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions(
            (new UnzerCredentialsConditionsTransfer())
                ->addId($unzerCredentialsTransfer->getIdUnzerCredentials())
                ->addType($unzerCredentialsTransfer->getType())
                ->addPublicKey($unzerCredentialsTransfer->getUnzerKeypair()->getPublicKey()),
        );

        //Act
        $unzerCredentialsCollectionTransfer = $this->facade->getUnzerCredentialsCollection($unzerCredentialsCriteriaTransfer);

        //Assert
        $this->assertEquals(1, $unzerCredentialsCollectionTransfer->getUnzerCredentials()->count());
    }

    /**
     * @return void
     */
    public function testGetUnzerCredentialsCollectionEmpty(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->haveMarketplaceUnzerCredentials($this->tester->haveStore());

        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions(
            (new UnzerCredentialsConditionsTransfer())
                ->addId($unzerCredentialsTransfer->getIdUnzerCredentials() + 1),
        );

        //Act
        $unzerCredentialsCollectionTransfer = $this->facade->getUnzerCredentialsCollection($unzerCredentialsCriteriaTransfer);

        //Assert
        $this->assertEquals(0, $unzerCredentialsCollectionTransfer->getUnzerCredentials()->count());
    }
}
