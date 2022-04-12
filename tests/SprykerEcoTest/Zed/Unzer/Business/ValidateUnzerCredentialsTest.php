<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\DataBuilder\StoreBuilder;
use Generated\Shared\DataBuilder\UnzerCredentialsBuilder;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Shared\Unzer\UnzerConstants as UnzerSharedConstants;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group Facade
 * @group ValidateUnzerCredentialsTest
 */
class ValidateUnzerCredentialsTest extends UnzerFacadeBaseTest
{
    /**
     * @var string
     */
    public const TOO_LONG_STRING = '6sVhY4A8ny34pBoZZG69uqAmJfH8ZrcvrhbLMrEFg10XyF0Km4sifwDBvHLyxkdh8VkDuc4wQgsRFfgDH5SkzmlUghzkGa8YPHCBvy4iV9QM6QTcYliVxspnaRBdoL5MQBdUmyxAh4u9LldnUgURa8Np9GxdPUx557VL35HUntQYsCSjnjdndpGcuRuT0QHxmkPIBYGee58MTAL6Cgawdp5aseF1eOMwDUQyM713vMW8lvlKNbOGttPJIleweUwnWjMt';

    /**
     * @var int
     */
    public const UNKNOWN_ID_UNZER_CREDENTIALS = 99999;

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeStandardReturnsSuccessfulUnzerCredentialsResponse(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $storeTransfer = (new StoreBuilder())->build();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
                UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
            ]))
            ->withStoreRelation([
                StoreRelationTransfer::STORES => [$storeTransfer],
                StoreRelationTransfer::ID_STORES => [$storeTransfer->getIdStore()],
            ])
            ->withUnzerKeypair()
            ->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->tester->getFacade()->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(0, $unzerCredentialsResponseTransfer->getMessages());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeStandardReturnsUnsuccessfulUnzerCredentialsResponseWhileUnzerKeypairIsNull(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $storeTransfer = (new StoreBuilder())->build();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
                UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
            ]))
            ->withStoreRelation([
                StoreRelationTransfer::STORES => [$storeTransfer],
                StoreRelationTransfer::ID_STORES => [$storeTransfer->getIdStore()],
            ])
            ->build();

        // Assert
        $this->expectException(NullValueException::class);
        $this->expectExceptionMessage('Property "unzerKeypair" of transfer `Generated\Shared\Transfer\UnzerCredentialsTransfer` is null.');

        // Act
        $this->tester->getFacade()->validateUnzerCredentials($unzerCredentialsTransfer);
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeStandardReturnsUnsuccessfulUnzerCredentialsResponseWhileStoreRelationAlreadyUsed(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $standardUnzerCredentials = $this->tester->haveStandardUnzerCredentials();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
                UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
                UnzerCredentialsTransfer::STORE_RELATION => $standardUnzerCredentials->getStoreRelation(),
            ]))
            ->withStoreRelation($standardUnzerCredentials->getStoreRelation()->toArray())
            ->withUnzerKeypair()
            ->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->tester->getFacade()->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('Chosen Store relation is already used', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeMarketplaceMainMerchantReturnsUnsuccessfulUnzerCredentialsResponseWhileMerchantReferenceDoesNotExist(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $marketplaceUnzerCredentailsTransfer = $this->tester->haveMarketplaceUnzerCredentials();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
                UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT,
                UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => $marketplaceUnzerCredentailsTransfer->getIdUnzerCredentials(),
                UnzerCredentialsTransfer::STORE_RELATION => [
                    StoreRelationTransfer::ID_STORES => [$marketplaceUnzerCredentailsTransfer->getStoreRelation()->getStores()->offsetGet(0)->getIdStore()],
                    StoreRelationTransfer::STORES => [$marketplaceUnzerCredentailsTransfer->getStoreRelation()->getStores()->offsetGet(0)],
                ]
            ]))
            ->withUnzerKeypair()
            ->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->tester->getFacade()->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('Merchant with provided reference does not exist!', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeMarketplaceMainMerchantReturnsUnsuccessfulUnzerCredentialsResponseWhileParentUnzerCredentialsAreInvalid(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::MERCHANT_REFERENCE => 'MyMerchantReference', MerchantTransfer::STATUS => 'approved']);
        $storeTransfer = (new StoreBuilder())->build();
        $mainMarketplaceMerchantUnzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT,
            UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => static::UNKNOWN_ID_UNZER_CREDENTIALS,
            UnzerCredentialsTransfer::MERCHANT_REFERENCE => $merchantTransfer->getMerchantReference(),
        ]))
            ->withStoreRelation([
                StoreRelationTransfer::STORES => [$storeTransfer],
                StoreRelationTransfer::ID_STORES => [$storeTransfer->getIdStore()],
            ])
            ->withUnzerKeypair()
            ->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->tester->getFacade()->validateUnzerCredentials($mainMarketplaceMerchantUnzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('Parent Unzer credentials not found!', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }
}
