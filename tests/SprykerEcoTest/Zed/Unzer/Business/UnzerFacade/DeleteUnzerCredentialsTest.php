<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business\UnzerFacade;

use Generated\Shared\DataBuilder\UnzerCredentialsBuilder;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants as UnzerSharedConstants;
use SprykerEcoTest\Zed\Unzer\Business\UnzerFacadeBaseTest;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group UnzerFacade
 */
class DeleteUnzerCredentialsTest extends UnzerFacadeBaseTest
{
    /**
     * @var string
     */
    protected const EMPTY_MERCHANT_REFERENCE = '';

    /**
     * @return void
     */
    public function testDeleteMarketplaceUnzerCredentialsSuccess(): void
    {
        // Arrange
        $unzerCredentialsTransfer = $this->tester->haveMarketplaceUnzerCredentialsWithMarketplaceMainMerchantUnzerCredentails();

        // Act
        $unzerCredentialsResponseTransfer = $this->tester->getFacade()->deleteUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testDeleteStandardUnzerCredentialsSuccess(): void
    {
        // Arrange
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials();

        // Act
        $unzerCredentialsResponseTransfer = $this->tester->getFacade()->deleteUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testDeleteUnzerCredentialsWithStandardUnzerCredentialsFailWhileUnzerCredentialsUnknown(): void
    {
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerSharedConstants::UNZER_CREDENTIALS_TYPE_STANDARD,
        ]))->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->tester->getFacade()->deleteUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('Unzer Credentials deletion failed.', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }

    /**
     * @return void
     */
    public function testDeleteUnzerCredentialsWithChildrenFailed(): void
    {
        // Arrange
        $marketplaceUnzerCredentials = $this->tester->haveMarketplaceUnzerCredentialsWithMarketplaceMainMerchantUnzerCredentails();
        $this->tester->haveMarketplaceMerchantUnzerCredentials([
            UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => $marketplaceUnzerCredentials->getIdUnzerCredentials(),
            UnzerCredentialsTransfer::MERCHANT_REFERENCE => static::EMPTY_MERCHANT_REFERENCE,
        ]);

        // Act
        $unzerCredentialsResponseTransfer = $this->tester->getFacade()->deleteUnzerCredentials($marketplaceUnzerCredentials);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
    }
}
