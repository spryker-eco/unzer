<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business\UnzerFacade;

use SprykerEcoTest\Zed\Unzer\Business\UnzerFacadeBaseTest;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group UnzerFacade
 * @group PerformPaymentMethodsImportTest
 */
class PerformPaymentMethodsImportTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testStandardCredentialsPerformPaymentMethodsImport(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTablesAreEmpty();
        $this->tester->ensurePaymentMethodTableIsEmpty();
        $this->tester->ensurePaymentProviderTableIsEmpty();
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials();

        // Act
        $this->tester->getFacade()->performPaymentMethodsImport($unzerCredentialsTransfer->getUnzerKeypairOrFail());

        // Assert
        $this->assertSame(1, $this->tester->getNumberOfPaymentProviders());
        $this->assertSame(2, $this->tester->getNumberOfPaymentMethods());
    }

    /**
     * @return void
     */
    public function testMarketplaceCredentialsPerformPaymentMethodsImport(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTablesAreEmpty();
        $this->tester->ensurePaymentMethodTableIsEmpty();
        $this->tester->ensurePaymentProviderTableIsEmpty();
        $unzerCredentialsTransfer = $this->tester->haveMarketplaceUnzerCredentialsWithMarketplaceMainMerchantUnzerCredentails();

        // Act
        $this->tester->getFacade()->performPaymentMethodsImport($unzerCredentialsTransfer->getUnzerKeypairOrFail());

        // Assert
        $this->assertSame(1, $this->tester->getNumberOfPaymentProviders());
        $this->assertSame(2, $this->tester->getNumberOfPaymentMethods());
    }
}
