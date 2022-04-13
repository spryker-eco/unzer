<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business\UnzerFacade;

use Generated\Shared\DataBuilder\UnzerCredentialsBuilder;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
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
class CreateUnzerCredentialsTest extends UnzerFacadeBaseTest
{
    /**
     * @return void
     */
    public function testCreateUnzerCredentialsReturnsSuccessfulUnzerCredentialsResponseWhileValidUnzerCredentialsGiven(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::ID_UNZER_CREDENTIALS => null
        ]))
            ->withUnzerKeypair([
                UnzerKeypairTransfer::KEYPAIR_ID => null
            ])->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->tester
            ->getFacade()
            ->createUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertSame($unzerCredentialsTransfer->getType(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getType());
        $this->assertSame($unzerCredentialsTransfer->getParentIdUnzerCredentials(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getParentIdUnzerCredentials());
        $this->assertNotNull($unzerCredentialsResponseTransfer->getUnzerCredentials()->getIdUnzerCredentials());
        $this->assertSame($unzerCredentialsTransfer->getConfigName(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getConfigName());
        $this->assertNotNull($unzerCredentialsResponseTransfer->getUnzerCredentials()->getUnzerKeypair()->getKeypairId());
        $this->assertSame($unzerCredentialsTransfer->getUnzerKeypair()->getPublicKey(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getUnzerKeypair()->getPublicKey());
        $this->assertSame($unzerCredentialsTransfer->getUnzerKeypair()->getPrivateKey(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getUnzerKeypair()->getPrivateKey());
        $this->assertSame($unzerCredentialsTransfer->getMerchantReference(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getMerchantReference());
        $this->assertSame($unzerCredentialsTransfer->getParticipantId(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getParticipantId());
        $this->assertSame($unzerCredentialsTransfer->getKeypairId(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getKeypairId());
    }

    /**
     * @return void
     */
    public function testCreateUnzerCredentialsThrowsNullValueExceptionWhileUnzerKeypairIsMissing(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder())->build();

        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->tester
            ->getFacade()
            ->createUnzerCredentials($unzerCredentialsTransfer);
    }

    /**
     * @return void
     */
    public function testCreateUnzerCredentialsThrowsNullValueExceptionWhileUnzerKeypairPublicKeyIsMissing(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::ID_UNZER_CREDENTIALS => null
        ]))
            ->withUnzerKeypair([
                UnzerKeypairTransfer::PUBLIC_KEY => null,
            ])->build();

        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->tester->getFacade()->createUnzerCredentials($unzerCredentialsTransfer);
    }

    /**
     * @return void
     */
    public function testCreateUnzerCredentialsThrowsNullValueExceptionWhileUnzerKeypairPrivateKeyIsMissing(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::ID_UNZER_CREDENTIALS => null
        ]))
            ->withUnzerKeypair([
                UnzerKeypairTransfer::PRIVATE_KEY => null,
            ])->build();

        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->tester->getFacade()->createUnzerCredentials($unzerCredentialsTransfer);
    }

    /**
     * @return void
     */
    public function testCreateUnzerCredentialsThrowsNullValueExceptionWhileTypeIsMissing(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => null
        ]))
            ->withUnzerKeypair()
            ->build();

        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->tester->getFacade()->createUnzerCredentials($unzerCredentialsTransfer);
    }

    /**
     * @return void
     */
    public function testCreateMarketplaceMainMerchantUnzerCredentialsThrowsNullValueExceptionWhileParentIdUnzerCredentialsIsMissing(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $marketplaceUnzerCredentials = $this->tester->haveMarketplaceUnzerCredentials();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerSharedConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT,
            UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => null,
            UnzerCredentialsTransfer::STORE_RELATION => [
                StoreRelationTransfer::STORES => [$marketplaceUnzerCredentials->getStoreRelation()->getStores()->offsetGet(0)],
                StoreRelationTransfer::ID_STORES => [$marketplaceUnzerCredentials->getStoreRelation()->getStores()->offsetGet(0)->getIdStore()],
            ],
        ]))
            ->withUnzerKeypair()
            ->build();

        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->tester->getFacade()->createUnzerCredentials($unzerCredentialsTransfer);
    }

    /**
     * @return void
     */
    public function testCreateMainMarketplaceUnzerCredentialsWhithChildUnzerCredentialsReturnsSuccessfulUnzerCredentialsResponseWhileValidUnzerCredentialsGiven(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
                UnzerCredentialsTransfer::TYPE => UnzerSharedConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE,
                UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => null,
                UnzerCredentialsTransfer::CHILD_UNZER_CREDENTIALS => (new UnzerCredentialsBuilder([
                    UnzerCredentialsTransfer::TYPE => UnzerSharedConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT,
                    UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => null,
                ]))
                    ->withUnzerKeypair()
                    ->build(),
            ]
        ))
            ->withUnzerKeypair([
                UnzerKeypairTransfer::KEYPAIR_ID => null
            ])->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->tester
            ->getFacade()
            ->createUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertSame($unzerCredentialsTransfer->getType(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getType());
        $this->assertSame($unzerCredentialsTransfer->getParentIdUnzerCredentials(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getParentIdUnzerCredentials());
        $this->assertNotNull($unzerCredentialsResponseTransfer->getUnzerCredentials()->getIdUnzerCredentials());
        $this->assertSame($unzerCredentialsTransfer->getConfigName(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getConfigName());
        $this->assertNotNull($unzerCredentialsResponseTransfer->getUnzerCredentials()->getUnzerKeypair()->getKeypairId());
        $this->assertSame($unzerCredentialsTransfer->getUnzerKeypair()->getPublicKey(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getUnzerKeypair()->getPublicKey());
        $this->assertSame($unzerCredentialsTransfer->getUnzerKeypair()->getPrivateKey(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getUnzerKeypair()->getPrivateKey());
        $this->assertSame($unzerCredentialsTransfer->getMerchantReference(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getMerchantReference());
        $this->assertSame($unzerCredentialsTransfer->getParticipantId(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getParticipantId());
        $this->assertNotNull($unzerCredentialsTransfer->getChildUnzerCredentials());
        $this->assertSame($unzerCredentialsTransfer->getChildUnzerCredentials()->getParentIdUnzerCredentials(), $unzerCredentialsTransfer->getIdUnzerCredentials());
        $this->assertSame($unzerCredentialsTransfer->getKeypairId(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getKeypairId());
    }

    /**
     * @return void
     */
    public function testCreateMainMerchantUnzerCredentialsReturnsSuccessfulUnzerCredentialsResponseWhileValidUnzerCredentialsGiven(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
                UnzerCredentialsTransfer::TYPE => UnzerSharedConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE,
                UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => null,
                UnzerCredentialsTransfer::CHILD_UNZER_CREDENTIALS => (new UnzerCredentialsBuilder([
                    UnzerCredentialsTransfer::TYPE => UnzerSharedConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT,
                    UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => null,
                ]))
                    ->withUnzerKeypair()
                    ->build(),
            ]
        ))
            ->withUnzerKeypair([
                UnzerKeypairTransfer::KEYPAIR_ID => null
            ])->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->tester
            ->getFacade()
            ->createUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertSame($unzerCredentialsTransfer->getType(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getType());
        $this->assertSame($unzerCredentialsTransfer->getParentIdUnzerCredentials(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getParentIdUnzerCredentials());
        $this->assertNotNull($unzerCredentialsResponseTransfer->getUnzerCredentials()->getIdUnzerCredentials());
        $this->assertSame($unzerCredentialsTransfer->getConfigName(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getConfigName());
        $this->assertNotNull($unzerCredentialsResponseTransfer->getUnzerCredentials()->getUnzerKeypair()->getKeypairId());
        $this->assertSame($unzerCredentialsTransfer->getUnzerKeypair()->getPublicKey(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getUnzerKeypair()->getPublicKey());
        $this->assertSame($unzerCredentialsTransfer->getUnzerKeypair()->getPrivateKey(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getUnzerKeypair()->getPrivateKey());
        $this->assertSame($unzerCredentialsTransfer->getMerchantReference(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getMerchantReference());
        $this->assertSame($unzerCredentialsTransfer->getParticipantId(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getParticipantId());
        $this->assertNotNull($unzerCredentialsTransfer->getChildUnzerCredentials());
        $this->assertSame($unzerCredentialsTransfer->getChildUnzerCredentials()->getParentIdUnzerCredentials(), $unzerCredentialsTransfer->getIdUnzerCredentials());
        $this->assertSame($unzerCredentialsTransfer->getKeypairId(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getKeypairId());
    }

    /**
     * @return void
     */
    public function testCreateMarketplaceMerchantUnzerCredentialsReturnsSuccessfulUnzerCredentialsResponseWhileValidUnzerCredentialsGiven(): void
    {
        // Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $marketplaceUnzerCredentials = $this->tester->haveMarketplaceUnzerCredentials();

        $unzerCredentialsTransfer = (new UnzerCredentialsBuilder([
            UnzerCredentialsTransfer::TYPE => UnzerSharedConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT,
            UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => $marketplaceUnzerCredentials->getIdUnzerCredentials(),
        ]))
            ->withUnzerKeypair([
                UnzerKeypairTransfer::KEYPAIR_ID => null
            ])
            ->build();

        // Act
        $unzerCredentialsResponseTransfer = $this->tester
            ->getFacade()
            ->createUnzerCredentials($unzerCredentialsTransfer);

        // Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertSame($unzerCredentialsTransfer->getType(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getType());
        $this->assertSame($unzerCredentialsTransfer->getParentIdUnzerCredentials(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getParentIdUnzerCredentials());
        $this->assertNotNull($unzerCredentialsResponseTransfer->getUnzerCredentials()->getIdUnzerCredentials());
        $this->assertSame($unzerCredentialsTransfer->getConfigName(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getConfigName());
        $this->assertNotNull($unzerCredentialsResponseTransfer->getUnzerCredentials()->getUnzerKeypair()->getKeypairId());
        $this->assertSame($unzerCredentialsTransfer->getUnzerKeypair()->getPublicKey(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getUnzerKeypair()->getPublicKey());
        $this->assertSame($unzerCredentialsTransfer->getUnzerKeypair()->getPrivateKey(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getUnzerKeypair()->getPrivateKey());
        $this->assertSame($unzerCredentialsTransfer->getMerchantReference(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getMerchantReference());
        $this->assertSame($unzerCredentialsTransfer->getParticipantId(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getParticipantId());
        $this->assertSame($unzerCredentialsTransfer->getKeypairId(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getKeypairId());
        $this->assertSame($marketplaceUnzerCredentials->getStoreRelation()->getIdStores(), $unzerCredentialsResponseTransfer->getUnzerCredentials()->getStoreRelation()->getIdStores());
    }
}
