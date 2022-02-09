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

class PerformPreSaveOrderStackFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testPerformPreSaveOrderStackMarketplace(): void
    {
        //Arrange
        $quoteTransfer = $this->tester->createMarketplaceQuoteTransfer();
        $unzerCredentialsTransfer = $this->tester->haveMarketplaceUnzerCredentials($quoteTransfer->getStoreOrFail());

        //Act
        $quoteTransfer = $this->facade->performPreSaveOrderStack($quoteTransfer);

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
        $quoteTransfer = $this->tester->createQuoteTransfer();
        $quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->setIsMarketplace(false);
        $unzerCredentialsTransfer = $this->tester->haveMarketplaceUnzerCredentials($quoteTransfer->getStoreOrFail());
        $this->tester->haveStandardUnzerCredentials($quoteTransfer->getStoreOrFail());

        //Act
        $quoteTransfer = $this->facade->performPreSaveOrderStack($quoteTransfer);

        //Assert
        $unzerPayment = $quoteTransfer->getPaymentOrFail()->getUnzerPayment();

        $this->assertInstanceOf(UnzerPaymentTransfer::class, $unzerPayment);
        $this->assertInstanceOf(UnzerCustomerTransfer::class, $unzerPayment->getCustomer());
        $this->assertInstanceOf(UnzerMetadataTransfer::class, $unzerPayment->getMetadata());
        $this->assertInstanceOf(UnzerKeypairTransfer::class, $unzerPayment->getUnzerKeypair());

        $this->assertSame($unzerCredentialsTransfer->getKeypairId(), $unzerPayment->getUnzerKeypair()->getKeypairId());
    }
}
