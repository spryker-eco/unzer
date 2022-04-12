<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\DataBuilder\UnzerCredentialsBuilder;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group UnzerFacade
 * @group CreateUnzerCredentialsFacadeTest
 */
class CreateUnzerCredentialsFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testCreateUnzerCredentials(): void
    {
        //Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder())
            ->withUnzerKeypair()
            ->build();

        //Act
        $unzerCredentialsResponseTransfer = $this->tester->getFacade()->createUnzerCredentials($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
    }
}
