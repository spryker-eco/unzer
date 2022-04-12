<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business\UnzerFacade;

use Generated\Shared\Transfer\UnzerNotificationTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;
use SprykerEcoTest\Zed\Unzer\Business\UnzerFacadeBaseTest;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group UnzerFacade
 * @group ProcessNotificationTest
 */
class ProcessNotificationTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testProcessNotificationSuccessful(): void
    {
        // Arrange
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials();
        $unzerNotificationTransfer = $this->tester->createUnzerNotificationTransfer([
            UnzerNotificationTransfer::PUBLIC_KEY => $unzerCredentialsTransfer->getUnzerKeypairOrFail()->getPublicKey(),
        ]);

        $unzerPaymentTransfer = $this->tester->createUnzerPaymentTransfer(false, false)->setUnzerKeypair($unzerCredentialsTransfer->getUnzerKeypair())->setId('s-pay-1234');
        $paymentTransfer = $this->tester->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_SOFORT)->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester->createQuoteTransfer()->setPayment($paymentTransfer);

        $saveOrderTransfer = $this->tester->haveOrder(
            [
            'unitPrice' => 72350,
            'sumPrice' => 72350,
            'orderReference' => $unzerPaymentTransfer->getOrderId(),
            ],
            'UnzerSofort01',
        );

        $this->tester->haveUnzerEntities($quoteTransfer, $saveOrderTransfer);

        $unzerNotificationTransfer = $this->tester->createUnzerNotificationTransfer([
                UnzerNotificationTransfer::PUBLIC_KEY => $unzerCredentialsTransfer->getUnzerKeypairOrFail()->getPublicKey(),
        ]);

        // Act
        $unzerNotificationTransfer = $this->tester->getFacade()->processNotification($unzerNotificationTransfer);

        // Assert
        $this->assertTrue($unzerNotificationTransfer->getIsProcessed());
    }

    /**
     * @return void
     */
    public function testProcessNotificationSkip(): void
    {
        // Arrange
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials();
        $unzerNotificationTransfer = $this->tester->createUnzerNotificationTransfer([
            UnzerNotificationTransfer::EVENT => 'disabled.event',
            UnzerNotificationTransfer::PUBLIC_KEY => $unzerCredentialsTransfer->getUnzerKeypairOrFail()->getPublicKey(),
        ]);

        // Act
        $unzerNotificationTransfer = $this->tester->getFacade()->processNotification($unzerNotificationTransfer);

        // Assert
        $this->assertTrue($unzerNotificationTransfer->getIsProcessed());
    }

    /**
     * @return void
     */
    public function testProcessNotificationTooEarly(): void
    {
        // Arrange
        $unzerNotificationTransfer = $this->tester->createUnzerNotificationTransfer([
            UnzerNotificationTransfer::PUBLIC_KEY => '',
        ]);

        // Act
        $unzerNotificationTransfer = $this->tester->getFacade()->processNotification($unzerNotificationTransfer);

        // Assert
        $this->assertFalse($unzerNotificationTransfer->getIsProcessed());
    }
}
