<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business\UnzerFacade;

use SprykerEcoTest\Zed\Unzer\Business\UnzerFacadeBaseTest;
use SprykerEcoTest\Zed\Unzer\UnzerBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group UnzerFacade
 * @group FilterMarketplacePaymentMethodsTest
 */
class FilterMarketplacePaymentMethodsTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testFilterPaymentMethodsShouldReturnPaymentMethodsWhileNoUnzerPaymentMethodGiven(): void
    {
        // Arrange
        $quoteTransfer = $this->tester->createQuoteTransfer();
        $paymentMethodsTransfer = $this->tester->createPaymentMethodsTransfer(false);
        $numberOfUnfilteredPaymentMethods = $paymentMethodsTransfer->getMethods()->count();

        // Act
        $filteredPaymentMethodsTransfer = $this->tester
            ->getFacade()
            ->filterMarketplacePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        // Assert
        $this->assertSame($numberOfUnfilteredPaymentMethods, $filteredPaymentMethodsTransfer->getMethods()->count());
    }

    /**
     * @return void
     */
    public function testFilterPaymentMethodsShouldReturnNoneUnzerPaymentMethodsAndStandardPaymentMethodsWhileStandardUnzerCredentialsGiven(): void
    {
        // Arrange
        $quoteTransfer = $this->tester->createUnzerStandardQuoteTransfer();
        $paymentMethodsTransfer = $this->tester->createPaymentMethodsTransfer();
        $numberOfUnfilteredPaymentMethods = $paymentMethodsTransfer->getMethods()->count();

        // Act
        $filteredPaymentMethodsTransfer = $this->tester
            ->getFacade()
            ->filterMarketplacePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        // Assert
        $this->assertSame(
            $numberOfUnfilteredPaymentMethods - count(UnzerBusinessTester::UNZER_MARKETPLACE_PAYMENT_METHODS),
            $filteredPaymentMethodsTransfer->getMethods()->count(),
        );
    }

    /**
     * @return void
     */
    public function testFilterPaymentMethodsShouldReturnNoneUnzerPaymentMethodsAndUnzerMarketplacePaymentMethodsWhileMultipeMerchantsGiven(): void
    {
        // Arrange
        $quoteTransfer = $this->tester->createMarketplaceQuoteTransfer();
        $paymentMethodsTransfer = $this->tester->createPaymentMethodsTransfer();
        $numberOfUnfilteredPaymentMethods = $paymentMethodsTransfer->getMethods()->count();

        // Act
        $filteredPaymentMethodsTransfer = $this->tester
            ->getFacade()
            ->filterMarketplacePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        // Assert
        $this->assertSame(
            $numberOfUnfilteredPaymentMethods - count(UnzerBusinessTester::UNZER_STANDARD_PAYMENT_METHODS),
            $filteredPaymentMethodsTransfer->getMethods()->count(),
        );
    }

    /**
     * @return void
     */
    public function testFilterPaymentMethodsShouldReturnUnfilteredPaymentMethodsWhileSingleMarketplaceMerchantGiven(): void
    {
        // Arrange
        $quoteTransfer = $this->tester->createMarketplaceMerchantQuoteTransfer();
        $paymentMethodsTransfer = $this->tester->createPaymentMethodsTransfer();
        $numberOfUnfilteredPaymentMethods = $paymentMethodsTransfer->getMethods()->count();

        // Act
        $filteredPaymentMethodsTransfer = $this->tester
            ->getFacade()
            ->filterMarketplacePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        // Assert
        $this->assertSame(
            $numberOfUnfilteredPaymentMethods,
            $filteredPaymentMethodsTransfer->getMethods()->count(),
        );
    }
}
