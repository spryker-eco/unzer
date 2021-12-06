<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

class CreateUnzerConfigFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testCreateUnzerConfigSuccess(): void
    {
        //Arrange
        $unzerConfigTransfer = $this->tester->createUnzerConfigTransfer();

        //Act
        $unzerConfigResponseTransfer = $this->facade->createUnzerConfig($unzerConfigTransfer);

        //Assert
        $this->assertTrue($unzerConfigResponseTransfer->getIsSuccessful());
    }
}
