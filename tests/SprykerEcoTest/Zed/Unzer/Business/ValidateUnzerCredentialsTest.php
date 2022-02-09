<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\DataBuilder\UnzerCredentialsBuilder;
use Generated\Shared\DataBuilder\UnzerKeypairBuilder;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;

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
     * @return void
     */
    public function testValidateUnzerCredentialsTypeStandardReturnsSuccessfulUnzerCredentialsResponse(): void
    {
        // Arrange
        $unzerKeypairTransfer = (new UnzerKeypairBuilder())->build();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
        ]))->build();
        $unzerCredentialsTransfer->setUnzerKeypair($unzerKeypairTransfer);

        // Act
        $unzerCredentialsResponseTransfer = $this->facade->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(0, $unzerCredentialsResponseTransfer->getMessages());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeStandardReturnsUnsuccessfulUnzerCredentialsResponseWhileConfigNameUndefined(): void
    {
        // Arrange
        $unzerKeypairTransfer = (new UnzerKeypairBuilder())->build();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
            UnzerCredentialsTransfer::CONFIG_NAME => '',
        ]))->build();
        $unzerCredentialsTransfer->setUnzerKeypair($unzerKeypairTransfer);

        // Act
        $unzerCredentialsResponseTransfer = $this->facade->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('[configName]', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getValue());
        $this->assertSame('This value should not be blank.', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeStandardReturnsUnsuccessfulUnzerCredentialsResponseWhileConfigNameTooLong(): void
    {
        // Arrange
        $unzerKeypairTransfer = (new UnzerKeypairBuilder())->build();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
            UnzerCredentialsTransfer::CONFIG_NAME => static::TOO_LONG_STRING,
        ]))->build();
        $unzerCredentialsTransfer->setUnzerKeypair($unzerKeypairTransfer);

        // Act
        $unzerCredentialsResponseTransfer = $this->facade->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('[configName]', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getValue());
        $this->assertSame('This value is too long. It should have 255 characters or less.', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeStandardReturnsUnsuccessfulUnzerCredentialsResponseWhileKeypairUndefined(): void
    {
        // Arrange
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
            UnzerCredentialsTransfer::UNZER_KEYPAIR => null,
        ]))->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->facade->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('[unzerKeypair]', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getValue());
        $this->assertSame('This value should not be blank.', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeStandardReturnsUnsuccessfulUnzerCredentialsResponseWhileKeypairPrivateKeyTooLong(): void
    {
        // Arrange
        $unzerKeypairTransfer = (new UnzerKeypairBuilder([
            UnzerKeypairTransfer::PRIVATE_KEY => null,
        ]))->build();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
            UnzerCredentialsTransfer::UNZER_KEYPAIR => null,
        ]))->build();
        $unzerCredentialsTransfer->setUnzerKeypair($unzerKeypairTransfer);

        // Act
        $unzerCredentialsResponseTransfer = $this->facade->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('[unzerKeypair][privateKey]', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getValue());
        $this->assertSame('This value should not be blank.', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeStandardReturnsUnsuccessfulUnzerCredentialsResponseWhileKeypairPrivateKeyUndefined(): void
    {
        // Arrange
        $unzerKeypairTransfer = (new UnzerKeypairBuilder([
            UnzerKeypairTransfer::PRIVATE_KEY => static::TOO_LONG_STRING,
        ]))->build();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
            UnzerCredentialsTransfer::UNZER_KEYPAIR => null,
        ]))->build();
        $unzerCredentialsTransfer->setUnzerKeypair($unzerKeypairTransfer);

        // Act
        $unzerCredentialsResponseTransfer = $this->facade->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('[unzerKeypair][privateKey]', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getValue());
        $this->assertSame('This value is too long. It should have 255 characters or less.', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeStandardReturnsUnsuccessfulUnzerCredentialsResponseWhileKeypairPublicKeyUndefined(): void
    {
        // Arrange
        $unzerKeypairTransfer = (new UnzerKeypairBuilder([
            UnzerKeypairTransfer::PUBLIC_KEY => null,
        ]))->build();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
            UnzerCredentialsTransfer::UNZER_KEYPAIR => null,
        ]))->build();
        $unzerCredentialsTransfer->setUnzerKeypair($unzerKeypairTransfer);

        // Act
        $unzerCredentialsResponseTransfer = $this->facade->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('[unzerKeypair][publicKey]', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getValue());
        $this->assertSame('This value should not be blank.', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeStandardReturnsUnsuccessfulUnzerCredentialsResponseWhileKeypairPublicKeyTooLong(): void
    {
        // Arrange
        $unzerKeypairTransfer = (new UnzerKeypairBuilder([
            UnzerKeypairTransfer::PUBLIC_KEY => static::TOO_LONG_STRING,
        ]))->build();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
            UnzerCredentialsTransfer::UNZER_KEYPAIR => null,
        ]))->build();
        $unzerCredentialsTransfer->setUnzerKeypair($unzerKeypairTransfer);

        // Act
        $unzerCredentialsResponseTransfer = $this->facade->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('[unzerKeypair][publicKey]', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getValue());
        $this->assertSame('This value is too long. It should have 255 characters or less.', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeStandardReturnsUnsuccessfulUnzerCredentialsResponseWhileKeypairPublicKeyNotUnique(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $standardUnzerCredentialsTransfer = $this->tester->haveUnzerCredentials($storeTransfer)->getUnzerCredentials();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
        ]))->withUnzerKeypair([
            UnzerKeypairTransfer::PUBLIC_KEY => $standardUnzerCredentialsTransfer->getUnzerKeypair()->getPublicKey(),
        ])
            ->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->facade->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('[unzerKeypair][publicKey]', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getValue());
        $this->assertSame('Unzer public key is already used.', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeStandardReturnsUnsuccessfulUnzerCredentialsResponseWhileStoreRelationAlreadyDefined(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $standardUnzerCredentials = $this->tester->haveUnzerCredentials($storeTransfer);

        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_STANDARD,
            UnzerCredentialsTransfer::STORE_RELATION => $storeTransfer->getName(),
        ]))->withUnzerKeypair()
            ->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->facade->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('[storeRelation]', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getValue());
        $this->assertSame('Store relation already defined.', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeMarketplaceMainMerchantReturnsUnsuccessfulUnzerCredentialsResponseWhileMerchantReferenceIsInvalid(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $mainMarketplaceUnzerCredentailsTransfer = $this->tester->haveMainMarketplaceUnzerCredentials($storeTransfer);
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT,
            UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => $mainMarketplaceUnzerCredentailsTransfer->getIdUnzerCredentials(),
        ]))->withUnzerKeypair()
            ->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->facade->validateUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('[merchantReference]', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getValue());
        $this->assertSame('Unknown merchant reference detected.', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }

    /**
     * @return void
     */
    public function testValidateUnzerCredentialsTypeMarketplaceMainMerchantReturnsUnsuccessfulUnzerCredentialsResponseWhileParentUnzerCredentialsAreInvalid(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $standardUnzerCredentialsTransfer = $this->tester->haveUnzerCredentials($storeTransfer)->getUnzerCredentials();
        $mainMarketplaceMerchantUnzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT,
            UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => $standardUnzerCredentialsTransfer->getIdUnzerCredentials(),
            UnzerCredentialsTransfer::MERCHANT_REFERENCE => null,
        ]))->withUnzerKeypair()
            ->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->facade->validateUnzerCredentials($mainMarketplaceMerchantUnzerCredentialsTransfer);

        // Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $unzerCredentialsResponseTransfer->getMessages());
        $this->assertSame('[parentIdUnzerCredentials]', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getValue());
        $this->assertSame('Invalid parent Unzer credentials detected.', $unzerCredentialsResponseTransfer->getMessages()->offsetGet(0)->getMessage());
    }
}
