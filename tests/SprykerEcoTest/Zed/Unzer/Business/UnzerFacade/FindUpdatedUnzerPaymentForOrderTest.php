<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business\UnzerFacade;

use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
use SprykerEco\Shared\Unzer\UnzerConfig;
use SprykerEco\Zed\Unzer\Communication\Plugin\Checkout\UnzerCheckoutDoSaveOrderPlugin;
use SprykerEcoTest\Zed\Unzer\Business\UnzerFacadeBaseTest;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group UnzerFacade
 * @group FindUpdatedUnzerPaymentForOrderTest
 */
class FindUpdatedUnzerPaymentForOrderTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testWillReturnNullForNonExistingOrder(): void
    {
        // Arrange
        $orderTransfer = (new OrderTransfer())->setOrderReference('fake-order-reference');

        // Act
        $unzerPaymentTransfer = $this->tester->getFacade()->findUpdatedUnzerPaymentForOrder($orderTransfer);

        // Assert
        $this->assertNull($unzerPaymentTransfer);
    }

    /**
     * @return void
     */
    public function testWillReturnPaymentTransferForExistingOrder(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $unzerCredentials = $this->tester->haveStandardUnzerCredentials();
        $unzerPaymentTransfer = $this->tester->createUnzerPaymentTransfer(false, false)->setUnzerKeypair($unzerCredentials->getUnzerKeypair());
        $paymentTransfer = $this->tester->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_SOFORT)->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester->createQuoteTransfer()->setPayment($paymentTransfer);

        $saveOrderTransfer = $this->tester->haveOrderFromQuote(
            $quoteTransfer,
            static::UNZER_SOFORT_STATE_MACHINE_PROCESS_NAME,
            [
                new UnzerCheckoutDoSaveOrderPlugin(),
            ],
        );
        $orderTransfer = (new OrderTransfer())->setOrderReference($saveOrderTransfer->getOrderReference());

        // Act
        $unzerPaymentTransfer = $this->tester->getFacade()->findUpdatedUnzerPaymentForOrder($orderTransfer);

        // Assert
        $this->assertNotNull($unzerPaymentTransfer);
        $this->assertSame($saveOrderTransfer->getOrderReference(), $unzerPaymentTransfer->getOrderId());
    }

    /**
     * @return void
     */
    public function testWillThrowExceptionWhenOrderReferenceIsMissing(): void
    {
        // Arrange
        $orderTransfer = (new OrderTransfer());

        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->tester->getFacade()->findUpdatedUnzerPaymentForOrder($orderTransfer);
    }
}
