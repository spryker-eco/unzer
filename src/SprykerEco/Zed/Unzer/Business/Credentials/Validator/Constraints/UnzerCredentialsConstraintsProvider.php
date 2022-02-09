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
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

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
     * @return \Symfony\Component\Validator\Constraints\Collection
     */
    public function getConstraintsCollectionByConfigType(int $unzerCredentialsType): Collection
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
     * @return \Symfony\Component\Validator\Constraints\Collection
     */
    protected function getDefaultConstraintsCollection(): Collection
    {
        return new Collection([
            'fields' => [
                UnzerCredentialsTransfer::CONFIG_NAME => $this->getConfigNameConstraints(),
                UnzerCredentialsTransfer::UNZER_KEYPAIR => $this->getKeypairConstraints(),
                UnzerCredentialsTransfer::STORE_RELATION => $this->getStoreRelationConstraints(),
            ],
            'allowExtraFields' => true,
        ]);
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\Collection
     */
    protected function getMarketplaceMainMerchantConstraintsCollection(): Collection
    {
        return new Collection([
            'fields' => [
                UnzerCredentialsTransfer::UNZER_KEYPAIR => $this->getKeypairConstraints(),
                UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => $this->getParentIdUnzerCredentialsConstraints(),
                UnzerCredentialsTransfer::MERCHANT_REFERENCE => $this->getMerchantReferenceConstraints(),
            ],
            'allowExtraFields' => true,
        ]);
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\Collection
     */
    protected function getMarketplaceMerchantConstraintsCollection(): Collection
    {
        return new Collection([
            'fields' => [
                UnzerCredentialsTransfer::UNZER_KEYPAIR => $this->getKeypairConstraints(),
                UnzerCredentialsTransfer::PARENT_ID_UNZER_CREDENTIALS => $this->getParentIdUnzerCredentialsConstraints(),
            ],
            'allowExtraFields' => true,
        ]);
    }

    /**
     * @return array
     */
    protected function getConfigNameConstraints(): array
    {
        return [
            new NotBlank(),
            new Length(['max' => 255]),
        ];
    }

    /**
     * @return array
     */
    protected function getKeypairConstraints(): array
    {
        return [
            new NotBlank(),
            new Type('array'),
            new Collection([
                'fields' => [
                    UnzerKeypairTransfer::PRIVATE_KEY => [
                        new NotBlank(),
                        new Length(['max' => 255]),
                    ],
                    UnzerKeypairTransfer::PUBLIC_KEY => [
                        new NotBlank(),
                        new Length(['max' => 255]),
                        new UniquePublicKeyConstraint($this->unzerReader),
                    ],
                ],
                'allowExtraFields' => true,
            ]),
        ];
    }

    /**
     * @return array
     */
    protected function getMerchantReferenceConstraints(): array
    {
        return [
            new ValidMerchantReferenceConstraint($this->merchantFacade),
        ];
    }

    /**
     * @return array
     */
    protected function getParentIdUnzerCredentialsConstraints(): array
    {
        return [
            new NotBlank(),
            new ValidParentUnzerCredentialsConstraint($this->unzerReader),
        ];
    }

    /**
     * @return array<int, \SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints\UniqueStoreRelationConstraint>
     */
    protected function getStoreRelationConstraints(): array
    {
        return [
            new UniqueStoreRelationConstraint($this->unzerReader),
        ];
    }
}
