<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business\UnzerFacade;

use Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;
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
    public function testResolveMarketplacePaymentUnzerCredentialsWithMainMarketplaceUnzerCredentialsWithinQuoteShouldReturnMainMarketplaceUnzerCredentials(): void
    {
        // Arrange
        $quoteTransfer = $this->tester->createMarketplaceQuoteTransfer();
        $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer = (new UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer())
            ->setPaymentMethodKey(UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_SOFORT)
            ->setQuote($quoteTransfer);

        // Act
        $resolvedUnzerCredentialsTransfer = $this->tester
            ->getFacade()
            ->resolveMarketplacePaymentUnzerCredentials($unzerMarketplacePaymentCredentialsResolverCriteriaTransfer);

        // Assert
        $this->assertSame(
            $quoteTransfer->getUnzerCredentialsOrFail()->getIdUnzerCredentials(),
            $resolvedUnzerCredentialsTransfer->getIdUnzerCredentials(),
        );
    }

    /**
     * @return void
     */
    public function testResolveMarketplacePaymentUnzerCredentialsWithMarketplaceMerchantUnzerCredentialsWithinQuoteShouldReturnMainMarketplaceUnzerCredentials(): void
    {
        // Arrange
        $quoteTransfer = $this->tester->createMarketplaceMerchantQuoteTransfer();
        $unzerMarketplacePaymentCredentialsResolverCrdsdsiteriaTransfer = (new UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer())
            ->setPaymentMethodKey(UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_SOFORT)
            ->setQuote($quoteTransfer);

        // Act
        $resolvedUnzerCredentialsTransfer = $this->tester
            ->getFacade()
            ->resolveMarketplacePaymentUnzerCredentials($unzerMarketplacePaymentCredentialsResolverCrdsdsiteriaTransfer);

        // Assert
        $this->assertSame(
            $quoteTransfer->getUnzerCredentialsOrFail()->getParentIdUnzerCredentials(),
            $resolvedUnzerCredentialsTransfer->getIdUnzerCredentials(),
        );
    }
}
