<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business\UnzerFacade;

use Generated\Shared\DataBuilder\ItemBuilder;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
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
 */
class ExpandQuoteWithUnzerCredentialsTest extends UnzerFacadeBaseTest
{
    /**
     * @var string
     */
    protected const STORE_NAME = 'DE';

    /**
     * @return void
     */
    public function testWillNotExpandQuoteWithoutItems(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME]);
        $this->tester->haveStandardUnzerCredentials();
        $quoteTransfer = (new QuoteTransfer())->setStore($storeTransfer);

        // Act
        $quoteTransfer = $this->tester->getFacade()->expandQuoteWithUnzerCredentials($quoteTransfer);

        // Assert
        $this->assertNull($quoteTransfer->getUnzerCredentials());
    }

    /**
     * @return void
     */
    public function testWillExpandQuoteWithUnzerCredentialForStandardSeller(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTablesAreEmpty();
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials();
        $quoteTransfer = (new QuoteTransfer())
            ->setStore($unzerCredentialsTransfer->getStoreRelation()->getStores()->offsetGet(0))
            ->addItem((new ItemBuilder([ItemTransfer::MERCHANT_REFERENCE => null]))->build())
            ->addItem((new ItemBuilder([ItemTransfer::MERCHANT_REFERENCE => null]))->build());

        // Act
        $quoteTransfer = $this->tester->getFacade()->expandQuoteWithUnzerCredentials($quoteTransfer);

        // Assert
        $this->assertNotNull($quoteTransfer->getUnzerCredentials());
        $this->assertSame($unzerCredentialsTransfer->getIdUnzerCredentials(), $quoteTransfer->getUnzerCredentials()->getIdUnzerCredentials());
        $this->assertSame(UnzerConstants::UNZER_CONFIG_TYPE_STANDARD, $quoteTransfer->getUnzerCredentials()->getType());
    }

    /**
     * @return void
     */
    public function testWillExpandQuoteWithUnzerCredentialForQuoteWithDifferentSellers(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTablesAreEmpty();
        $unzerCredentialsTransfer = $this->tester->haveMarketplaceUnzerCredentialsWithMarketplaceMainMerchantUnzerCredentails();
        $quoteTransfer = (new QuoteTransfer())
            ->setStore($unzerCredentialsTransfer->getStoreRelation()->getStores()->offsetGet(0))
            ->addItem((new ItemBuilder([ItemTransfer::MERCHANT_REFERENCE => null]))->build())
            ->addItem((new ItemBuilder([ItemTransfer::MERCHANT_REFERENCE => UnzerBusinessTester::MERCHANT_REFERENCE]))->build());

        // Act
        $quoteTransfer = $this->tester->getFacade()->expandQuoteWithUnzerCredentials($quoteTransfer);

        // Assert
        $this->assertNotNull($quoteTransfer->getUnzerCredentials());
        $this->assertSame(UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE, $quoteTransfer->getUnzerCredentials()->getType());
    }

    /**
     * @return void
     */
    public function testWillExpandQuoteWithUnzerCredentialForQuoteMerchantSellers(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTablesAreEmpty();
        $unzerCredentialsTransfer = $this->tester->haveMarketplaceMerchantUnzerCredentials([
            UnzerCredentialsTransfer::MERCHANT_REFERENCE => UnzerBusinessTester::MERCHANT_REFERENCE,
        ]);
        $quoteTransfer = (new QuoteTransfer())
            ->setStore($unzerCredentialsTransfer->getStoreRelation()->getStores()->offsetGet(0))
            ->addItem((new ItemBuilder([ItemTransfer::MERCHANT_REFERENCE => UnzerBusinessTester::MERCHANT_REFERENCE]))->build())
            ->addItem((new ItemBuilder([ItemTransfer::MERCHANT_REFERENCE => UnzerBusinessTester::MERCHANT_REFERENCE]))->build());

        // Act
        $quoteTransfer = $this->tester->getFacade()->expandQuoteWithUnzerCredentials($quoteTransfer);

        // Assert
        $this->assertNotNull($quoteTransfer->getUnzerCredentials());
        $this->assertSame(UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT, $quoteTransfer->getUnzerCredentials()->getType());
        $this->assertSame(UnzerBusinessTester::MERCHANT_REFERENCE, $quoteTransfer->getUnzerCredentials()->getMerchantReference());
    }
}
