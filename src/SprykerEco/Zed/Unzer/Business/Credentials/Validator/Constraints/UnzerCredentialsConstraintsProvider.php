<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints;

use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;

class UnzerCredentialsConstraintsProvider implements UnzerCredentialsConstraintsProviderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface $merchantFacade
     */
    public function __construct(UnzerReaderInterface $unzerReader, UnzerToMerchantFacadeInterface $merchantFacade)
    {
        $this->unzerReader = $unzerReader;
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * @param int $unzerCredentialsType
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    public function getConstraintsCollectionByConfigType(int $unzerCredentialsType): array
    {
        switch ($unzerCredentialsType) {
            case UnzerConstants::UNZER_CONFIG_TYPE_STANDARD:
            case UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE:
                return $this->getDefaultConstraintsCollection();
            case UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT:
                return $this->getMarketplaceMainMerchantConstraintsCollection();
            case UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT:
                return $this->getMarketplaceMerchantConstraintsCollection();
            default:
                throw new UnzerException(sprintf('Invalid Unzer credentials type "%s" detected.', $unzerCredentialsType));
        }
    }

    /**
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    protected function getDefaultConstraintsCollection(): array
    {
        return array_merge(
            [new TransferConstraint([UnzerCredentialsTransfer::CONFIG_NAME => $this->getConfigNameConstraint()])],
            [new TransferConstraint([UnzerCredentialsTransfer::UNZER_KEYPAIR => $this->getKeypairConstraints()])],
            [new UniquePublicKeyConstraint($this->unzerReader)],
            $this->getStoreRelationConstraints(),
        );
    }

    /**
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    protected function getMarketplaceMainMerchantConstraintsCollection(): array
    {
        return array_merge(
            [new TransferConstraint([UnzerCredentialsTransfer::UNZER_KEYPAIR => $this->getKeypairConstraints()])],
            [new UniquePublicKeyConstraint($this->unzerReader)],
            $this->getStoreRelationConstraints(),
            $this->getMerchantReferenceConstraints(),
        );
    }

    /**
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    protected function getMarketplaceMerchantConstraintsCollection(): array
    {
        return array_merge(
            [new TransferConstraint([UnzerCredentialsTransfer::UNZER_KEYPAIR => $this->getKeypairConstraints()])],
            [new UniquePublicKeyConstraint($this->unzerReader)],
            $this->getParentIdUnzerCredentialsConstraints(),
            $this->getMerchantReferenceConstraints(),
        );
    }

    /**
     * @return \Symfony\Component\Validator\Constraint
     */
    protected function getConfigNameConstraint(): SymfonyConstraint
    {
        return new Sequentially([
                new NotBlank(),
                new Length(['max' => 255]),
            ]);
    }

    /**
     * @return \Symfony\Component\Validator\Constraint
     */
    protected function getKeypairConstraints(): SymfonyConstraint
    {
        return new Sequentially([
            new TransferConstraint([
                UnzerKeypairTransfer::PRIVATE_KEY => $this->getRegularStringConstraint(),
                UnzerKeypairTransfer::PUBLIC_KEY => $this->getRegularStringConstraint(),
            ]),
        ]);
    }

    /**
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    protected function getMerchantReferenceConstraints(): array
    {
        return [
            new ValidMerchantReferenceConstraint($this->merchantFacade),
        ];
    }

    /**
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    protected function getParentIdUnzerCredentialsConstraints(): array
    {
        return [
            new NotBlank(),
            new ValidParentUnzerCredentialsConstraint($this->unzerReader),
        ];
    }

    /**
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    protected function getStoreRelationConstraints(): array
    {
        return [
            new UniqueStoreRelationConstraint($this->unzerReader),
        ];
    }

    /**
     * @return \Symfony\Component\Validator\Constraint
     */
    protected function getRegularStringConstraint(): SymfonyConstraint
    {
        return new Sequentially([
            new NotBlank(),
            new Length(['max' => 255]),
        ]);
    }
}
