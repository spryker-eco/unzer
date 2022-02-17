<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
use SprykerEco\Zed\Unzer\Communication\Plugin\Checkout\UnzerCheckoutDoSaveOrderPlugin;

class FindUpdatedUnzerPaymentForOrderFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @var string
     */
    protected const STATE_MACHINE_PROCESS_NAME = 'UnzerMarketplaceBankTransfer01';

    /**
     * @return void
     */
    public function testWillReturnNullForNonExistingOrder(): void
    {
        // Arrange
        $orderTransfer = (new OrderTransfer())->setOrderReference('fake-order-reference');

        // Act
        $unzerPaymentTransfer = $this->facade->findUpdatedUnzerPaymentForOrder($orderTransfer);

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

        $quoteTransfer = $this->tester->createMarketplaceQuoteTransfer();
        $this->tester->haveUnzerCredentials($quoteTransfer->getStoreOrFail());
        $saveOrderTransfer = $this->tester->haveOrderFromQuote(
            $quoteTransfer,
            static::STATE_MACHINE_PROCESS_NAME,
            [
                new UnzerCheckoutDoSaveOrderPlugin(),
            ],
        );
        $orderTransfer = (new OrderTransfer())->setOrderReference($saveOrderTransfer->getOrderReference());

        // Act
        $unzerPaymentTransfer = $this->facade->findUpdatedUnzerPaymentForOrder($orderTransfer);

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
        $this->expectException(NullValueException::class);

        // Act
        $this->facade->findUpdatedUnzerPaymentForOrder($orderTransfer);
    }
}
