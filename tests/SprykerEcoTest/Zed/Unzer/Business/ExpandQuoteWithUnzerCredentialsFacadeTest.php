<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\DataBuilder\ItemBuilder;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEcoTest\Zed\Unzer\UnzerZedTester;


class ExpandQuoteWithUnzerCredentialsFacadeTest extends UnzerFacadeBaseTest
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
        $this->tester->haveUnzerCredentials($storeTransfer);
        $quoteTransfer = (new QuoteTransfer())->setStore($storeTransfer);

        // Act
        $quoteTransfer = $this->facade->expandQuoteWithUnzerCredentials($quoteTransfer);

        // Assert
        $this->assertNull($quoteTransfer->getUnzerCredentials());
    }

    /**
     * @return void
     */
    public function testWillExpandQuoteWithUnzerCredentialForStandardSeller(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME]);
        $unzerCredentialsTransfer = $this->tester->haveUnzerCredentials($storeTransfer)->getUnzerCredentialsOrFail();
        $quoteTransfer = (new QuoteTransfer())
            ->setStore($storeTransfer)
            ->addItem((new ItemBuilder([ItemTransfer::MERCHANT_REFERENCE => null]))->build())
            ->addItem((new ItemBuilder([ItemTransfer::MERCHANT_REFERENCE => null]))->build());

        // Act
        $quoteTransfer = $this->facade->expandQuoteWithUnzerCredentials($quoteTransfer);

        // Assert
        $this->assertNotNull($quoteTransfer->getUnzerCredentials());
        $this->assertSame($unzerCredentialsTransfer->getIdUnzerCredentials(), $quoteTransfer->getUnzerCredentials()->getIdUnzerCredentials());
        $this->assertSame(UnzerConstants::UNZER_CONFIG_TYPE_STANDARD, $quoteTransfer->getUnzerCredentials()->getType());
    }

    /**
     * @return void
     */
    public function testWillExpandQuoteWithUnzerCredentialForMerchantSeller(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME]);
        $this->tester->haveMarketplaceUnzerCredentials($storeTransfer);
        $quoteTransfer = (new QuoteTransfer())
            ->setStore($storeTransfer)
            ->addItem((new ItemBuilder([ItemTransfer::MERCHANT_REFERENCE => null]))->build())
            ->addItem((new ItemBuilder([ItemTransfer::MERCHANT_REFERENCE => UnzerZedTester::MERCHANT_REFERENCE]))->build());

        // Act
        $quoteTransfer = $this->facade->expandQuoteWithUnzerCredentials($quoteTransfer);

        // Assert
        $this->assertNotNull($quoteTransfer->getUnzerCredentials());
        $this->assertSame(UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE, $quoteTransfer->getUnzerCredentials()->getType());
    }
}
