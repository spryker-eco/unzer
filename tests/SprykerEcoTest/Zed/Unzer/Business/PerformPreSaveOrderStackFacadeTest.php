<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerMetadataTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group Facade
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
        $unzerPaymentTransfer = $this->tester->createUnzerPaymentTransfer(true, false);
        $paymentTransfer = $this->tester->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_BANK_TRANSFER)->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester->createQuoteTransfer()->setPayment($paymentTransfer);
        $unzerCredentialsTransfer = $this->tester->haveMarketplaceUnzerCredentials([
            UnzerCredentialsTransfer::STORE_RELATION => $this->tester->createStoreRelation($quoteTransfer->getStore()),
        ], [
            UnzerCredentialsTransfer::STORE_RELATION => $this->tester->createStoreRelation($quoteTransfer->getStore()),
        ]);
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
        $unzerPaymentTransfer = $this->tester->createUnzerPaymentTransfer(false, false);
        $paymentTransfer = $this->tester->createPaymentTransfer(UnzerConfig::PAYMENT_METHOD_KEY_BANK_TRANSFER)->setUnzerPayment($unzerPaymentTransfer);
        $quoteTransfer = $this->tester->createQuoteTransfer()->setPayment($paymentTransfer);
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials([
            UnzerCredentialsTransfer::STORE_RELATION => $this->tester->createStoreRelation($quoteTransfer->getStore()),
        ]);

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
