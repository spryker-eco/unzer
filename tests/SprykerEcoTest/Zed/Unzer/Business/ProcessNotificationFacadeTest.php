<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

class ProcessNotificationFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testProcessNotificationSuccessful(): void
    {
        //Arrange
        $unzerNotificationTransfer = $this->tester->createUnzerNotificationTransfer();
        $unzerCredentialsTransfer = $this->tester->haveUnzerCredentials($this->tester->haveStore())->getUnzerCredentials();
        $unzerNotificationTransfer->setPublicKey($unzerCredentialsTransfer->getUnzerKeypair()->getPublicKey());
        $this->tester->haveUnzerEntities(
            $this->tester->createQuoteTransfer(),
            $this->tester->createOrder()
        );

        //Act
        $unzerNotificationTransfer = $this->facade->processNotification($unzerNotificationTransfer);

        //Assert
        $this->assertTrue($unzerNotificationTransfer->getIsProcessed());
    }

    /**
     * @return void
     */
    public function testProcessNotificationSkip(): void
    {
        //Arrange
        $unzerNotificationTransfer = $this->tester->createUnzerNotificationTransfer();
        $unzerNotificationTransfer->setEvent('disabled.event');

        //Act
        $unzerNotificationTransfer = $this->facade->processNotification($unzerNotificationTransfer);

        //Assert
        $this->assertTrue($unzerNotificationTransfer->getIsProcessed());
    }

    /**
     * @return void
     */
    public function testProcessNotificationTooEarly(): void
    {
        //Arrange
        $unzerNotificationTransfer = $this->tester->createUnzerNotificationTransfer();

        //Act
        $unzerNotificationTransfer = $this->facade->processNotification($unzerNotificationTransfer);

        //Assert
        $this->assertFalse($unzerNotificationTransfer->getIsProcessed());
    }
}
