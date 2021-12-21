<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use SprykerEcoTest\Zed\Unzer\UnzerZedTester;

class FilterMarketplacePaymentMethodsFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testFilterMarketplacePaymentMethodsNotFiltered(): void
    {
        //Arrange
        $quoteTransfer = $this->tester->createMarketplaceQuoteTransfer();
        $paymentMethodsTransfer = $this->tester->createPaymentMethodsTransfer();
        $paymentMethodsCount = $paymentMethodsTransfer->getMethods()->count();

        //Act
        $paymentMethodsTransfer = $this->facade->filterMarketplacePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        //Assert
        $this->assertSame($paymentMethodsCount, $paymentMethodsTransfer->getMethods()->count());
    }

    /**
     * @return void
     */
    public function testFilterMarketplacePaymentMethodsFiltered(): void
    {
        //Arrange
        $quoteTransfer = $this->tester->createQuoteTransfer();
        $paymentMethodsTransfer = $this->tester->createPaymentMethodsTransfer();
        $paymentMethodsCount = $paymentMethodsTransfer->getMethods()->count();

        //Act
        $paymentMethodsTransfer = $this->facade->filterMarketplacePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        //Assert
        $this->assertSame(
            $paymentMethodsCount - count(UnzerZedTester::UNZER_MARKETPLACE_PAYMENT_METHODS),
            $paymentMethodsTransfer->getMethods()->count(),
        );
    }
}