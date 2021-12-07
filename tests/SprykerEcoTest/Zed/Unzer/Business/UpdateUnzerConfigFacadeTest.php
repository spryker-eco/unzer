<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

class UpdateUnzerCredentialsFacadeTest extends UnzerFacadeBaseTest
{
    protected const ANOTHER_PUBLIC_KEY = 'key2';
    const ANOTHER_PARTICIPANT_ID = 'part2';
    const ANOTHER_PRIVATE_KEY = 'key3';

    /**
     * @return void
     */
    public function testUpdateUnzerCredentialsSuccess(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->haveUnzerCredentials()->getUnzerCredentials();
        $unzerCredentialsTransfer->setParticipantId(static::ANOTHER_PARTICIPANT_ID);
        $unzerCredentialsTransfer->getUnzerKeypairOrFail()->setPublicKey(static::ANOTHER_PUBLIC_KEY);
        $unzerCredentialsTransfer->getUnzerKeypairOrFail()->setPrivateKey(static::ANOTHER_PRIVATE_KEY);

        //Act
        $unzerCredentialsResponseTransfer = $this->facade->updateUnzerCredentials($unzerCredentialsTransfer);

        //Assert
        $this->assertTrue($unzerCredentialsResponseTransfer->getIsSuccessful());
        $this->assertSame(static::ANOTHER_PUBLIC_KEY, $unzerCredentialsResponseTransfer->getUnzerCredentialsOrFail()->getUnzerKeypairOrFail()->getPublicKey());
        $this->assertSame(static::ANOTHER_PRIVATE_KEY, $unzerCredentialsResponseTransfer->getUnzerCredentialsOrFail()->getUnzerKeypairOrFail()->getPrivateKey());
        $this->assertSame(static::ANOTHER_PARTICIPANT_ID, $unzerCredentialsResponseTransfer->getUnzerCredentialsOrFail()->getParticipantId());
    }

    /**
     * @return void
     */
    public function testUpdateUnzerCredentialsThrowsException(): void
    {
    }
}
