<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

class UpdateUnzerCredentialsFacadeTest extends UnzerFacadeBaseTest
{
    /**
     * @var string
     */
    protected const ANOTHER_PUBLIC_KEY = 'key2';

    /**
     * @var string
     */
    protected const ANOTHER_PARTICIPANT_ID = 'part2';

    /**
     * @var string
     */
    protected const ANOTHER_PRIVATE_KEY = 'key3';

    /**
     * @var string
     */
    protected const UNKNOWN_ID = 'unknown';

    /**
     * @return void
     */
    public function testUpdateUnzerCredentialsSuccess(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials($this->tester->haveStore());
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
    public function testUpdateUnzerCredentialsFail(): void
    {
        //Arrange
        $unzerCredentialsTransfer = $this->tester->haveStandardUnzerCredentials($this->tester->haveStore());
        $unzerCredentialsTransfer->setIdUnzerCredentials(static::UNKNOWN_ID);

        //Act
        $unzerCredentialsResponseTransfer = $this->facade->updateUnzerCredentials($unzerCredentialsTransfer);

        //Assert
        $this->assertFalse($unzerCredentialsResponseTransfer->getIsSuccessful());
    }
}
