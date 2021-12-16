<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use SprykerEco\Shared\Unzer\UnzerConfig;
use SprykerEcoTest\Zed\Unzer\UnzerZedTester;

class ExecutePostSaveHookFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @var array
     */
    protected const UNZER_STANDARD_PAYMENT_METHODS = [
        // will be implemented along with regular payments
    ];

    /**
     * @var array
     */
    protected const UNZER_MARKETPLACE_PAYMENT_METHODS = [
        UnzerConfig::PAYMENT_METHOD_MARKETPLACE_BANK_TRANSFER,
        UnzerConfig::PAYMENT_METHOD_MARKETPLACE_CREDIT_CARD,
        UnzerConfig::PAYMENT_METHOD_MARKETPLACE_SOFORT,
    ];

    /**
     * @return void
     */
    public function testExecutePostSaveHookMarketplacePayments(): void
    {
        //Arrange
        $quoteTransfer = $this->tester->createMarketplaceQuoteTransfer();
        $this->tester->haveMarketplaceUnzerCredentials($quoteTransfer->getStore());

        foreach (static::UNZER_MARKETPLACE_PAYMENT_METHODS as $paymentMethod) {
            $quoteTransfer->getPaymentOrFail()->setPaymentSelection($paymentMethod);
            $this->tester->haveUnzerEntities($quoteTransfer, $this->tester->createOrder());
            $checkoutResponseTransfer = $this->tester->createCheckoutResponseTransfer();

            //Act
            $this->facade->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);

            //Assert
            $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
            $this->assertSame(UnzerZedTester::UNZER_REDIRECT_URL, $checkoutResponseTransfer->getRedirectUrl());
        }
    }

    /**
     * @return void
     */
    public function testExecutePostSaveHookRegularPayments()
    {
        // will be implemented along with regular payments
    }
}
