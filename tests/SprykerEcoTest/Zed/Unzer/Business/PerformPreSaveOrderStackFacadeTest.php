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
use SprykerEcoTest\Zed\Unzer\UnzerZedTester;

class PerformPreSaveOrderStackFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testPerformPreSaveOrderStackMarketplace(): void
    {
        //Arrange
        $quoteTransfer = $this->tester->createMarketplaceQuoteTransfer();
        $this->tester->haveMarketplaceUnzerCredentials($quoteTransfer->getStoreOrFail());

        //Act
        $quoteTransfer = $this->facade->performPreSaveOrderStack($quoteTransfer);

        //Assert
        $unzerPayment = $quoteTransfer->getPaymentOrFail()->getUnzerPayment();

        $this->assertInstanceOf(UnzerPaymentTransfer::class, $unzerPayment);
        $this->assertInstanceOf(UnzerCustomerTransfer::class, $unzerPayment->getCustomer());
        $this->assertInstanceOf(UnzerMetadataTransfer::class, $unzerPayment->getMetadata());
        $this->assertInstanceOf(UnzerKeypairTransfer::class, $unzerPayment->getUnzerKeypair());

        $this->assertSame(UnzerZedTester::UNZER_MAIN_MARKETPLACE_KEYPAIR_ID, $unzerPayment->getUnzerKeypair()->getKeypairId());
    }

    /**
     * @return void
     */
    public function testPerformPreSaveOrderStackStandard(): void
    {
        //Arrange
        $quoteTransfer = $this->tester->createQuoteTransfer();
        $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->setIsMarketplace(false);
        $this->tester->haveMarketplaceUnzerCredentials($quoteTransfer->getStoreOrFail());
        $this->tester->haveUnzerCredentials($quoteTransfer->getStoreOrFail());

        //Act
        $quoteTransfer = $this->facade->performPreSaveOrderStack($quoteTransfer);

        //Assert
        $unzerPayment = $quoteTransfer->getPaymentOrFail()->getUnzerPayment();

        $this->assertInstanceOf(UnzerPaymentTransfer::class, $unzerPayment);
        $this->assertInstanceOf(UnzerCustomerTransfer::class, $unzerPayment->getCustomer());
        $this->assertInstanceOf(UnzerMetadataTransfer::class, $unzerPayment->getMetadata());
        $this->assertInstanceOf(UnzerKeypairTransfer::class, $unzerPayment->getUnzerKeypair());

        $this->assertSame(UnzerZedTester::UNZER_MAIN_MARKETPLACE_KEYPAIR_ID, $unzerPayment->getUnzerKeypair()->getKeypairId());
    }
}
