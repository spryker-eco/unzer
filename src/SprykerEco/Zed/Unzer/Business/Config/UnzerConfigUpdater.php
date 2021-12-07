<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Config;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\UnzerConfigResponseTransfer;
use Generated\Shared\Transfer\UnzerConfigTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriterInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface;

class UnzerConfigUpdater implements UnzerConfigUpdaterInterface
{
    use TransactionTrait;

    /**
     * @var string
     */
    protected const MESSAGE_UPDATE_ERROR = 'It is impossible to update this Unzer config';

    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface
     */
    protected $unzerEntityManager;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Config\UnzerConfigStoreRelationUpdaterInterface
     */
    protected $unzerConfigStoreRelationUpdater;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriterInterface
     */
    protected $unzerVaultWriter;

    /**
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface $unzerEntityManager
     * @param \SprykerEco\Zed\Unzer\Business\Config\UnzerConfigStoreRelationUpdaterInterface $unzerConfigStoreRelationUpdater
     * @param \SprykerEco\Zed\Unzer\Business\Writer\UnzerVaultWriterInterface $unzerVaultWriter
     */
    public function __construct(
        UnzerEntityManagerInterface $unzerEntityManager,
        UnzerConfigStoreRelationUpdaterInterface $unzerConfigStoreRelationUpdater,
        UnzerVaultWriterInterface $unzerVaultWriter
    ) {
        $this->unzerEntityManager = $unzerEntityManager;
        $this->unzerConfigStoreRelationUpdater = $unzerConfigStoreRelationUpdater;
        $this->unzerVaultWriter = $unzerVaultWriter;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerConfigTransfer $unzerConfigTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigResponseTransfer
     */
    public function updateUnzerConfig(UnzerConfigTransfer $unzerConfigTransfer): UnzerConfigResponseTransfer
    {
        return $this->getTransactionHandler()->handleTransaction(function () use ($unzerConfigTransfer) {
            return $this->executeUpdateUnzerConfigTransaction($unzerConfigTransfer);
        });
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerConfigTransfer $unzerConfigTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigResponseTransfer
     */
    protected function executeUpdateUnzerConfigTransaction(UnzerConfigTransfer $unzerConfigTransfer): UnzerConfigResponseTransfer
    {
        $unzerConfigTransfer = $this->unzerEntityManager->updateUnzerConfig($unzerConfigTransfer);
        if ($unzerConfigTransfer === null) {
            return (new UnzerConfigResponseTransfer())
                ->setIsSuccessful(false)
                ->setMessages([
                    (new MessageTransfer())
                    ->setValue(static::MESSAGE_UPDATE_ERROR),
                ]);
        }

        $this->unzerVaultWriter->storeUnzerPrivateKey(
            $unzerConfigTransfer->getKeypairId(),
            $unzerConfigTransfer->getUnzerKeypairOrFail()->getPrivateKey(),
        );

        if ($unzerConfigTransfer->getStoreRelation() !== null) {
            $storeRelationTransfer = $unzerConfigTransfer->getStoreRelation()
                ->setIdEntity($unzerConfigTransfer->getIdUnzerConfigOrFail());
            $this->unzerConfigStoreRelationUpdater->update($storeRelationTransfer);
        }

        return (new UnzerConfigResponseTransfer())
            ->setIsSuccessful(true)
            ->setUnzerConfig($unzerConfigTransfer);
    }
}
