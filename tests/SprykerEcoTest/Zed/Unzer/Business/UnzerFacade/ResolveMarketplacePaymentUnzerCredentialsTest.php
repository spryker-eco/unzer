<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business\UnzerFacade;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer;
use SprykerEcoTest\Zed\Unzer\Business\UnzerFacadeBaseTest;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group UnzerFacade
 * @group ResolveMarketplacePaymentUnzerCredentialsTest
 */
class ResolveMarketplacePaymentUnzerCredentialsTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testResolveMarketplacePaymentUnzerCredentials(): void
    {
        // Arrange
        $quoteTransfer = new QuoteTransfer();
        $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer = (new UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer())
            ->setPaymentMethodKey('UnzerMarketplaceCreditCard')
            ->setQuote($quoteTransfer);

        // Act
        $this->tester->getFacade()->resolveMarketplacePaymentUnzerCredentials($unzerMarketplacePaymentCredentialsResolverCriteriaTransfer);

        // Assert
        $this->assertTrue(true);
    }
}
