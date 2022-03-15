<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsResponseTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;

class UnzerCredentialsDeleter implements UnzerCredentialsDeleterInterface
{
    /**
     * @var string
     */
    protected const MESSAGE_DELETE_NOT_AVAILABLE = 'Unzer Credentials cannot be deleted until has child elements.';

    /**
     * @var string
     */
    protected const MESSAGE_DELETE_FAILED = 'Unzer Credentials deletion failed.';

    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface
     */
    protected $unzerEntityManager;

    /**
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface $unzerRepository
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface $unzerEntityManager
     */
    public function __construct(
        UnzerRepositoryInterface $unzerRepository,
        UnzerEntityManagerInterface $unzerEntityManager
    ) {
        $this->unzerRepository = $unzerRepository;
        $this->unzerEntityManager = $unzerEntityManager;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    public function deleteUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer
    {
        $unzerCredentialsResponseTransfer = new UnzerCredentialsResponseTransfer();

        if ($this->hasChildCredentials($unzerCredentialsTransfer)) {
            $unzerCredentialsResponseTransfer->addMessage((new MessageTransfer())->setMessage(static::MESSAGE_DELETE_NOT_AVAILABLE));

            return $unzerCredentialsResponseTransfer->setIsSuccessful(false);
        }

        if ($unzerCredentialsTransfer->getType() === UnzerConstants::UNZER_CONFIG_TYPE_MAIN_MARKETPLACE) {
            if (!$this->deleteMarketplaceMainMerchantUnzerCredentials($unzerCredentialsTransfer)) {
                return $this->buildFailedUnzerCredentialsResponseTransfer($unzerCredentialsResponseTransfer);
            }
        }

        if ($this->unzerEntityManager->deleteUnzerCredentials($unzerCredentialsTransfer)) {
            return $unzerCredentialsResponseTransfer->setIsSuccessful(true);
        }

        return $this->buildFailedUnzerCredentialsResponseTransfer($unzerCredentialsResponseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return bool
     */
    protected function hasChildCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): bool
    {
        $unzerCredentialsType = $unzerCredentialsTransfer->getType();
        if (
            $unzerCredentialsType === UnzerConstants::UNZER_CONFIG_TYPE_STANDARD ||
            $unzerCredentialsType === UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT
        ) {
            return false;
        }

        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions(
            (new UnzerCredentialsConditionsTransfer())->addParentId($unzerCredentialsTransfer->getIdUnzerCredentialsOrFail()),
        );
        $childUnzerCredentialsCollectionTransfer = $this->unzerRepository->findUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);

        return $childUnzerCredentialsCollectionTransfer->getUnzerCredentials()->count() > 1;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return bool
     */
    protected function deleteMarketplaceMainMerchantUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): bool
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions(
            (new UnzerCredentialsConditionsTransfer())
                ->addParentId($unzerCredentialsTransfer->getIdUnzerCredentialsOrFail())
                ->addType(UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT),
        );

        $unzerCredentialsCollectionTransfer = $this->unzerRepository
            ->findUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);

        if ($unzerCredentialsCollectionTransfer->getUnzerCredentials()->count() !== 1) {
            return false;
        }

        $mainMerchantUnzerCredentialsTransfer = $unzerCredentialsCollectionTransfer->getUnzerCredentials()[0];

        return $this->unzerEntityManager->deleteUnzerCredentials($mainMerchantUnzerCredentialsTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsResponseTransfer
     */
    protected function buildFailedUnzerCredentialsResponseTransfer(
        UnzerCredentialsResponseTransfer $unzerCredentialsResponseTransfer
    ): UnzerCredentialsResponseTransfer {
        $unzerCredentialsResponseTransfer->addMessage((new MessageTransfer())->setMessage(static::MESSAGE_DELETE_FAILED));

        return $unzerCredentialsResponseTransfer->setIsSuccessful(false);
    }
}
