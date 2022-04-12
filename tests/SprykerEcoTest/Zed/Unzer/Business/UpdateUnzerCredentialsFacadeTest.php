<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Generated\Shared\DataBuilder\UnzerKeypairBuilder;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Unzer
 * @group Business
 * @group Facade
 * @group UpdateUnzerCredentialsFacadeTest
 */
class UpdateUnzerCredentialsFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @var string
     */
    protected const ANOTHER_PUBLIC_KEY = 'key2';

    /**
     * @var string
     */
    protected const ANOTHER_PARTICIPANT_ID = '123ABC456ABC73DB2BBE1A016A028B46';

    /**
     * @var string
     */
    protected const ANOTHER_PRIVATE_KEY = 'key3';

    /**
     * @var int
     */
    protected const UNKNOWN_ID = 9999999;

    /**
     * @return void
     */
    public function testUpdateMarketplaceMainUnzerCredentialsStoreRelationsAndParticipantIdReturnsSuccessful(): void
    {
        //Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $storeTransfer = $this->tester->haveStore();
        $unzerCredentialsTransfer = $this->tester
            ->haveMarketplaceUnzerCredentialsWithMarketplaceMainMerchantUnzerCredentails()
            ->setParticipantId(static::ANOTHER_PARTICIPANT_ID);

        $unzerCredentialsTransfer->getStoreRelation()
            ->addIdStores($storeTransfer->getIdStore())
            ->addStores($storeTransfer);

        //Act
        $unzerCredentialsResponseTransfer = $this->tester
            ->getFacade()
            ->updateUnzerCredentials($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertSame(
            static::ANOTHER_PARTICIPANT_ID,
            $unzerCredentialsResponseTransfer->getUnzerCredentials()
                ->getParticipantId(),
        );
        $this->assertSame(2, $unzerCredentialsResponseTransfer->getUnzerCredentials()->getStoreRelation()->getStores()->count());
    }

    /**
     * @return void
     */
    public function testUpdateStandardUnzerCredentialsKeypairReturnsResponseSuccessful(): void
    {
        //Arrange
        $this->tester->ensureUnzerCredentialsTableIsEmpty();
        $unzerKeypairTransfer = (new UnzerKeypairBuilder())->build();
        $unzerCredentialsTransfer = $this->tester
            ->haveStandardUnzerCredentials()
            ->setUnzerKeypair($unzerKeypairTransfer);

        //Act
        $unzerCredentialsResponseTransfer = $this->tester
            ->getFacade()
            ->updateUnzerCredentials($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertSame(
            $unzerKeypairTransfer->getPublicKey(),
            $unzerCredentialsResponseTransfer->getUnzerCredentials()
                ->getUnzerKeypair()
                ->getPublicKey(),
        );
        $this->assertSame(
            $unzerKeypairTransfer->getPrivateKey(),
            $unzerCredentialsResponseTransfer->getUnzerCredentials()
                ->getUnzerKeypair()
                ->getPrivateKey(),
        );
    }
}
