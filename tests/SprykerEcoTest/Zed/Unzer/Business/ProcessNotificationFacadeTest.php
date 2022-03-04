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
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials($this->tester->haveStore());
        $unzerNotificationTransfer = $this->tester->createUnzerNotificationTransfer($unzerCredentialsTransfer->getUnzerKeypair()->getPublicKey());
        $unzerNotificationTransfer->setPublicKey($unzerCredentialsTransfer->getUnzerKeypair()->getPublicKey());

        $quoteTransfer = $this->tester->createQuoteTransfer();
        $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->setUnzerKeypair($unzerCredentialsTransfer->getUnzerKeypair());
        $this->tester->haveUnzerEntities(
            $quoteTransfer,
            $this->tester->createOrder(),
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
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials($this->tester->haveStore());
        $unzerNotificationTransfer = $this->tester->createUnzerNotificationTransfer($unzerCredentialsTransfer->getUnzerKeypair()->getPublicKey());
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
        $unzerNotificationTransfer = $this->tester->createUnzerNotificationTransfer('');

        //Act
        $unzerNotificationTransfer = $this->facade->processNotification($unzerNotificationTransfer);

        //Assert
        $this->assertFalse($unzerNotificationTransfer->getIsProcessed());
    }
}
