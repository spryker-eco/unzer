<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

class UpdateUnzerConfigFacadeTest extends UnzerFacadeBaseTest
{
    protected const ANOTHER_PUBLIC_KEY = 'key2';
    const ANOTHER_PARTICIPANT_ID = 'part2';
    const ANOTHER_PRIVATE_KEY = 'key3';

    /**
     * @return void
     */
    public function testUpdateUnzerConfigSuccess(): void
    {
        //Arrange
        $unzerConfigTransfer = $this->tester->haveUnzerConfig()->getUnzerConfig();
        $unzerConfigTransfer->setParticipantId(static::ANOTHER_PARTICIPANT_ID);
        $unzerConfigTransfer->getUnzerKeypairOrFail()->setPublicKey(static::ANOTHER_PUBLIC_KEY);
        $unzerConfigTransfer->getUnzerKeypairOrFail()->setPrivateKey(static::ANOTHER_PRIVATE_KEY);

        //Act
        $unzerConfigResponseTransfer = $this->facade->updateUnzerConfig($unzerConfigTransfer);

        //Assert
        $this->assertTrue($unzerConfigResponseTransfer->getIsSuccessful());
        $this->assertSame(static::ANOTHER_PUBLIC_KEY, $unzerConfigResponseTransfer->getUnzerConfigOrFail()->getUnzerKeypairOrFail()->getPublicKey());
        $this->assertSame(static::ANOTHER_PRIVATE_KEY, $unzerConfigResponseTransfer->getUnzerConfigOrFail()->getUnzerKeypairOrFail()->getPrivateKey());
        $this->assertSame(static::ANOTHER_PARTICIPANT_ID, $unzerConfigResponseTransfer->getUnzerConfigOrFail()->getParticipantId());
    }

    /**
     * @return void
     */
    public function testUpdateUnzerConfigThrowsException(): void
    {
    }
}
