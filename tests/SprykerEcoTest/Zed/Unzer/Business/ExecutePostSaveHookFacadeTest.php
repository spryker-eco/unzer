<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\DataBuilder\UnzerPaymentResourceBuilder;
use SprykerEco\Shared\Unzer\UnzerConfig;
use SprykerEcoTest\Zed\Unzer\UnzerZedTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group Facade
 * @group ExecutePostSaveHookFacadeTest
 */
class ExecutePostSaveHookFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testExecutePostSaveHookReturnsExternalRedirectWhileUnzerBankTransferIsGiven(): void
    {
        //Arrange
        $unzerPaymentTransfer = $this->tester
            ->createUnzerPaymentTransfer(false, false);
        $paymentTransfer = $this->tester
            ->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_BANK_TRANSFER)
            ->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester
            ->createQuoteTransfer()
            ->setPayment($paymentTransfer);
        $checkoutResponseTransfer = $this->tester->createCheckoutResponseTransfer();

        $saveOrderTransfer = $this->tester->haveOrder(
            [
                'unitPrice' => 72350,
                'sumPrice' => 72350,
                'orderReference' => 'DE--1',
            ],
            'UnzerBankTransfer01',
        );

        $this->tester->haveUnzerEntities($quoteTransfer, $saveOrderTransfer);

        //Act
        $this->tester->getFacade()->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        //Assert
        $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
        $this->assertSame(UnzerZedTester::UNZER_REDIRECT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function testExecutePostSaveHookReturnsExternalRedirectWhileUnzerSofortIsGiven(): void
    {
        //Arrange
        $unzerPaymentTransfer = $this->tester
            ->createUnzerPaymentTransfer(false, false);
        $paymentTransfer = $this->tester
            ->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_SOFORT)
            ->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester
            ->createQuoteTransfer()
            ->setPayment($paymentTransfer);
        $checkoutResponseTransfer = $this->tester->createCheckoutResponseTransfer();

        $saveOrderTransfer = $this->tester->haveOrder(
            [
            'unitPrice' => 72350,
            'sumPrice' => 72350,
            'orderReference' => 'DE--2',
            ],
            'UnzerSofort01',
        );

        $this->tester->haveUnzerEntities($quoteTransfer, $saveOrderTransfer);

        //Act
        $this->tester->getFacade()->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        //Assert
        $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
        $this->assertSame(UnzerZedTester::UNZER_REDIRECT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function testExecutePostSaveHookReturnsExternalRedirectWhileUnzerCreditCardIsGiven(): void
    {
        //Arrange
        $unzerPaymentTransfer = $this->tester
            ->createUnzerPaymentTransfer(false, true)
            ->setPaymentResource((new UnzerPaymentResourceBuilder())->build());
        $paymentTransfer = $this->tester
            ->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_CREDIT_CARD)
            ->setUnzerCreditCard($unzerPaymentTransfer)
            ->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester
            ->createQuoteTransfer()
            ->setPayment($paymentTransfer);
        $checkoutResponseTransfer = $this->tester->createCheckoutResponseTransfer();

        $saveOrderTransfer = $this->tester->haveOrder(
            [
                'unitPrice' => 72350,
                'sumPrice' => 72350,
                'orderReference' => 'DE--3',
            ],
            'UnzerCreditCard01',
        );

        $this->tester->haveUnzerEntities($quoteTransfer, $saveOrderTransfer);

        //Act
        $this->tester->getFacade()->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        //Assert
        $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
        $this->assertSame(UnzerZedTester::UNZER_REDIRECT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function testExecutePostSaveHookReturnsExternalRedirectWhileUnzerMarketplaceBankTransferIsGiven(): void
    {
        //Arrange
        $unzerPaymentTransfer = $this->tester
            ->createUnzerPaymentTransfer(true, false);
        $paymentTransfer = $this->tester
            ->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_BANK_TRANSFER)
            ->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester
            ->createQuoteTransfer()
            ->setPayment($paymentTransfer);
        $checkoutResponseTransfer = $this->tester->createCheckoutResponseTransfer();

        $saveOrderTransfer = $this->tester->haveOrder(
            [
            'unitPrice' => 72350,
            'sumPrice' => 72350,
            'orderReference' => 'DE--4',
            ],
            'UnzerMarketplaceBankTransfer01',
        );

        $this->tester->haveUnzerEntities($quoteTransfer, $saveOrderTransfer);

        //Act
        $this->tester->getFacade()->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        //Assert
        $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
        $this->assertSame(UnzerZedTester::UNZER_REDIRECT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function testExecutePostSaveHookReturnsExternalRedirectWhileUnzerMarketplaceSofortIsGiven(): void
    {
        //Arrange
        $unzerPaymentTransfer = $this->tester
            ->createUnzerPaymentTransfer(true, false);
        $paymentTransfer = $this->tester
            ->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_SOFORT)
            ->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester
            ->createQuoteTransfer()
            ->setPayment($paymentTransfer);
        $checkoutResponseTransfer = $this->tester->createCheckoutResponseTransfer();
        $saveOrderTransfer = $this->tester->haveOrder(
            [
            'unitPrice' => 72350,
            'sumPrice' => 72350,
            'orderReference' => 'DE--5',
            ],
            'UnzerMarketplaceSofort01',
        );
        $this->tester->haveUnzerEntities($quoteTransfer, $saveOrderTransfer);

        //Act
        $this->tester->getFacade()->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        //Assert
        $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
        $this->assertSame(UnzerZedTester::UNZER_REDIRECT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function testExecutePostSaveHookReturnsExternalRedirectWhileUnzerMarketplaceCreditCardIsGiven(): void
    {
        //Arrange
        $unzerPaymentTransfer = $this->tester
            ->createUnzerPaymentTransfer(true, true)
            ->setPaymentResource((new UnzerPaymentResourceBuilder())->build());
        $paymentTransfer = $this->tester
            ->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_CREDIT_CARD)
            ->setUnzerPayment($unzerPaymentTransfer)
            ->setUnzerMarketplaceCreditCard($unzerPaymentTransfer);
        $quoteTransfer = $this->tester
            ->createQuoteTransfer()
            ->setPayment($paymentTransfer);
        $checkoutResponseTransfer = $this->tester->createCheckoutResponseTransfer();
        $saveOrderTransfer = $this->tester->haveOrder(
            [
            'unitPrice' => 72350,
            'sumPrice' => 72350,
            'orderReference' => 'DE--6',
            ],
            'UnzerMarketplaceCreditCard01',
        );
        $this->tester->haveUnzerEntities($quoteTransfer, $saveOrderTransfer);
        //Act
        $this->tester->getFacade()->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        //Assert
        $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
        $this->assertSame(UnzerZedTester::UNZER_REDIRECT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }
}
