<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business\UnzerFacade;

use Generated\Shared\DataBuilder\CheckoutResponseBuilder;
use Generated\Shared\DataBuilder\UnzerPaymentResourceBuilder;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;
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
 * @group ExecutePostSaveHookTest
 */
class ExecutePostSaveHookTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testExecutePostSaveHookReturnsExternalRedirectWhileUnzerBankTransferIsGiven(): void
    {
        // Arrange
        $saveOrderTransfer = $this->tester->haveOrder([
            ItemTransfer::UNIT_PRICE_TO_PAY_AGGREGATION => 72350,
        ], static::UNZER_BANK_TRANSFER_STATE_MACHINE_PROCESS_NAME);
        $unzerPaymentTransfer = $this->tester->createUnzerPaymentTransfer(false, false)->setOrderId($saveOrderTransfer->getOrderReference());
        $paymentTransfer = $this->tester->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_BANK_TRANSFER)->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester->createQuoteTransfer()->setPayment($paymentTransfer);
        $checkoutResponseTransfer = (new CheckoutResponseBuilder([
            CheckoutResponseTransfer::SAVE_ORDER => $saveOrderTransfer->toArray(),
        ]))->build();
        $quoteTransfer->setItems($saveOrderTransfer->getOrderItems());

        $this->tester->haveUnzerEntities($quoteTransfer, $saveOrderTransfer);

        // Act
        $this->tester->getFacade()->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
        $this->assertSame(UnzerBusinessTester::UNZER_REDIRECT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function testExecutePostSaveHookReturnsExternalRedirectWhileUnzerSofortIsGiven(): void
    {
        // Arrange
        $saveOrderTransfer = $this->tester->haveOrder(
            [
                ItemTransfer::UNIT_PRICE_TO_PAY_AGGREGATION => 72350,
            ],
            static::UNZER_SOFORT_STATE_MACHINE_PROCESS_NAME,
        );
        $unzerPaymentTransfer = $this->tester->createUnzerPaymentTransfer(false, false)->setOrderId($saveOrderTransfer->getOrderReference());
        $paymentTransfer = $this->tester->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_SOFORT)->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester->createQuoteTransfer()->setPayment($paymentTransfer);
        $checkoutResponseTransfer = (new CheckoutResponseBuilder([
            CheckoutResponseTransfer::SAVE_ORDER => $saveOrderTransfer->toArray(),
        ]))->build();
        $quoteTransfer->setItems($saveOrderTransfer->getOrderItems());

        $this->tester->haveUnzerEntities($quoteTransfer, $saveOrderTransfer);

        // Act
        $this->tester->getFacade()->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
        $this->assertSame(UnzerBusinessTester::UNZER_REDIRECT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function testExecutePostSaveHookReturnsExternalRedirectWhileUnzerCreditCardIsGiven(): void
    {
        // Arrange
        $saveOrderTransfer = $this->tester->haveOrder(
            [
                ItemTransfer::UNIT_PRICE_TO_PAY_AGGREGATION => 72350,
            ],
            static::UNZER_CREDIT_CARD_STATE_MACHINE_PROCESS_NAME,
        );
        $unzerPaymentTransfer = $this->tester->createUnzerPaymentTransfer(false, true)
            ->setPaymentResource((new UnzerPaymentResourceBuilder())->build())
            ->setOrderId($saveOrderTransfer->getOrderReference());
        $paymentTransfer = $this->tester->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_CREDIT_CARD)->setUnzerPayment($unzerPaymentTransfer)->setUnzerCreditCard($unzerPaymentTransfer);
        $quoteTransfer = $this->tester->createQuoteTransfer()->setPayment($paymentTransfer);
        $checkoutResponseTransfer = (new CheckoutResponseBuilder([
            CheckoutResponseTransfer::SAVE_ORDER => $saveOrderTransfer->toArray(),
        ]))->build();
        $quoteTransfer->setItems($saveOrderTransfer->getOrderItems());

        $this->tester->haveUnzerEntities($quoteTransfer, $saveOrderTransfer);

        // Act
        $this->tester->getFacade()->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
        $this->assertSame(UnzerBusinessTester::UNZER_REDIRECT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function testExecutePostSaveHookReturnsExternalRedirectWhileUnzerMarketplaceBankTransferIsGiven(): void
    {
        // Arrange
        $saveOrderTransfer = $this->tester->haveOrder(
            [
                ItemTransfer::UNIT_PRICE_TO_PAY_AGGREGATION => 72350,
            ],
            static::UNZER_MARKETPLACE_BANK_TRANSFER_STATE_MACHINE_PROCESS_NAME,
        );
        $unzerPaymentTransfer = $this->tester
            ->createUnzerPaymentTransfer(true, false);
        $paymentTransfer = $this->tester
            ->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_BANK_TRANSFER)
            ->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester
            ->createQuoteTransfer()
            ->setPayment($paymentTransfer);
        $checkoutResponseTransfer = (new CheckoutResponseBuilder([
            CheckoutResponseTransfer::SAVE_ORDER => $saveOrderTransfer->toArray(),
        ]))->build();
        $quoteTransfer->setItems($saveOrderTransfer->getOrderItems());

        $this->tester->haveUnzerEntities($quoteTransfer, $saveOrderTransfer);

        // Act
        $this->tester->getFacade()->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
        $this->assertSame(UnzerBusinessTester::UNZER_REDIRECT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function testExecutePostSaveHookReturnsExternalRedirectWhileUnzerMarketplaceSofortIsGiven(): void
    {
        // Arrange
        $saveOrderTransfer = $this->tester->haveOrder(
            [
                ItemTransfer::UNIT_PRICE_TO_PAY_AGGREGATION => 72350,
            ],
            static::UNZER_MARKETPLACE_SOFORT_STATE_MACHINE_PROCESS_NAME,
        );
        $unzerPaymentTransfer = $this->tester
            ->createUnzerPaymentTransfer(true, false);
        $paymentTransfer = $this->tester
            ->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_SOFORT)
            ->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester
            ->createQuoteTransfer()
            ->setPayment($paymentTransfer);
        $checkoutResponseTransfer = (new CheckoutResponseBuilder([
            CheckoutResponseTransfer::SAVE_ORDER => $saveOrderTransfer->toArray(),
        ]))->build();
        $quoteTransfer->setItems($saveOrderTransfer->getOrderItems());
        $this->tester->haveUnzerEntities($quoteTransfer, $saveOrderTransfer);

        // Act
        $this->tester->getFacade()->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
        $this->assertSame(UnzerBusinessTester::UNZER_REDIRECT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function testExecutePostSaveHookReturnsExternalRedirectWhileUnzerMarketplaceCreditCardIsGiven(): void
    {
        // Arrange
        $saveOrderTransfer = $this->tester->haveOrder(
            [
                ItemTransfer::UNIT_PRICE_TO_PAY_AGGREGATION => 72350,
            ],
            static::UNZER_MARKETPLACE_CREDIT_CARD_STATE_MACHINE_PROCESS_NAME,
        );
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
        $checkoutResponseTransfer = (new CheckoutResponseBuilder([
            CheckoutResponseTransfer::SAVE_ORDER => $saveOrderTransfer->toArray(),
        ]))->build();
        $quoteTransfer->setItems($saveOrderTransfer->getOrderItems());
        $this->tester->haveUnzerEntities($quoteTransfer, $saveOrderTransfer);

        // Act
        $this->tester->getFacade()->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
        $this->assertSame(UnzerBusinessTester::UNZER_REDIRECT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }
}
