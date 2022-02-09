<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\DataBuilder\UnzerCredentialsBuilder;
use Generated\Shared\DataBuilder\UnzerKeypairBuilder;

class CreateUnzerCredentialsFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testCreateUnzerCredentials(): void
    {
        //Arrange
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder())->build();
        $unzerKeypairTransfer = (new UnzerKeypairBuilder())->build();
        $unzerCredentialsTransfer->setUnzerKeypair($unzerKeypairTransfer);

        //Act
        $unzerCredentialsResponseTransfer = $this->facade->createUnzerCredentials($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
    }
}
