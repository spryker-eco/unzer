<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerMetadataTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group UnzerFacade
 * @group PerformPreSaveOrderStackFacadeTest
 */
class PerformPreSaveOrderStackFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testPerformPreSaveOrderStackMarketplace(): void
    {
        //Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $unzerPaymentTransfer = $this->tester->createUnzerPaymentTransfer(true, false);
        $paymentTransfer = $this->tester->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_BANK_TRANSFER)->setUnzerPayment($unzerPaymentTransfer);
        $unzerCredentialsTransfer = $this->tester->haveMarketplaceUnzerCredentialsWithMarketplaceMainMerchantUnzerCredentails();
        $quoteTransfer = $this->tester->createQuoteTransfer()->setPayment($paymentTransfer)->setStore($unzerCredentialsTransfer->getStoreRelation()->getStores()->offsetGet(0));
        $quoteTransfer->getPayment()->getUnzerPayment()->setUnzerKeypair($unzerCredentialsTransfer->getUnzerKeypair());

        //Act
        $quoteTransfer = $this->tester->getFacade()->performPreSaveOrderStack($quoteTransfer);

        //Assert
        $unzerPayment = $quoteTransfer->getPaymentOrFail()->getUnzerPayment();

        $this->assertInstanceOf(UnzerPaymentTransfer::class, $unzerPayment);
        $this->assertInstanceOf(UnzerCustomerTransfer::class, $unzerPayment->getCustomer());
        $this->assertInstanceOf(UnzerMetadataTransfer::class, $unzerPayment->getMetadata());
        $this->assertInstanceOf(UnzerKeypairTransfer::class, $unzerPayment->getUnzerKeypair());

        $this->assertSame($unzerCredentialsTransfer->getUnzerKeypair()->getKeypairId(), $unzerPayment->getUnzerKeypair()->getKeypairId());
    }

    /**
     * @return void
     */
    public function testPerformPreSaveOrderStackStandard(): void
    {
        //Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $unzerPaymentTransfer = $this->tester->createUnzerPaymentTransfer(false, false);
        $paymentTransfer = $this->tester->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_SOFORT)->setUnzerPayment($unzerPaymentTransfer);
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials();
        $quoteTransfer = $this->tester->createQuoteTransfer()->setPayment($paymentTransfer)->setStore($unzerCredentialsTransfer->getStoreRelation()->getStores()->offsetGet(0));

        //Act
        $quoteTransfer = $this->tester->getFacade()->performPreSaveOrderStack($quoteTransfer);

        //Assert
        $unzerPayment = $quoteTransfer->getPaymentOrFail()->getUnzerPayment();

        $this->assertInstanceOf(UnzerPaymentTransfer::class, $unzerPayment);
        $this->assertInstanceOf(UnzerCustomerTransfer::class, $unzerPayment->getCustomer());
        $this->assertInstanceOf(UnzerMetadataTransfer::class, $unzerPayment->getMetadata());
        $this->assertInstanceOf(UnzerKeypairTransfer::class, $unzerPayment->getUnzerKeypair());

        $this->assertSame($unzerCredentialsTransfer->getKeypairId(), $unzerPayment->getUnzerKeypair()->getKeypairId());
    }
}
