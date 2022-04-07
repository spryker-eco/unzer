<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group Facade
 * @group GetUnzerCredentialsCollectionFacadeTest
 */
class GetUnzerCredentialsCollectionFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testGetUnzerCredentialsCollectionExists(): void
    {
        //Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials();

        //Act
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions(
            (new UnzerCredentialsConditionsTransfer())
                ->addId($unzerCredentialsTransfer->getIdUnzerCredentials())
                ->addType($unzerCredentialsTransfer->getType())
                ->addPublicKey($unzerCredentialsTransfer->getUnzerKeypair()->getPublicKey()),
        );
        $unzerCredentialsCollectionTransfer = $this->tester->getFacade()->getUnzerCredentialsCollection($unzerCredentialsCriteriaTransfer);

        //Assert
        $this->assertSame(1, $unzerCredentialsCollectionTransfer->getUnzerCredentials()->count());
    }

    /**
     * @return void
     */
    public function testGetUnzerCredentialsCollectionEmpty(): void
    {
        //Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $this->tester->haveStandardUnzerCredentials();

        //Act
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions(
            (new UnzerCredentialsConditionsTransfer())->addId(9999),
        );
        $unzerCredentialsCollectionTransfer = $this->tester->getFacade()->getUnzerCredentialsCollection($unzerCredentialsCriteriaTransfer);

        //Assert
        $this->assertEquals(0, $unzerCredentialsCollectionTransfer->getUnzerCredentials()->count());
    }
}
