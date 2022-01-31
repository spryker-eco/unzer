<?php

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
     * @var UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @var UnzerEntityManagerInterface
     */
    protected $unzerEntityManager;

    /**
     * @param UnzerRepositoryInterface $unzerRepository
     * @param UnzerEntityManagerInterface $unzerEntityManager
     */
    public function __construct(
        UnzerRepositoryInterface $unzerRepository,
        UnzerEntityManagerInterface $unzerEntityManager
    )
    {
        $this->unzerRepository = $unzerRepository;
        $this->unzerEntityManager = $unzerEntityManager;
    }


    /**
     * @param UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return UnzerCredentialsResponseTransfer
     */
    public function deleteUnzerCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsResponseTransfer
    {
        $unzerCredentialsResponseTransfer = new UnzerCredentialsResponseTransfer();

        if ($this->haveChildCredentials($unzerCredentialsTransfer)) {
            $unzerCredentialsResponseTransfer->addMessage((new MessageTransfer())->setMessage(static::MESSAGE_DELETE_NOT_AVAILABLE));

            return $unzerCredentialsResponseTransfer->setIsSuccessful(false);
        }

        if ($this->unzerEntityManager->deleteUnzerCredentials($unzerCredentialsTransfer)) {
            return $unzerCredentialsResponseTransfer->setIsSuccessful(true);
        }

        $unzerCredentialsResponseTransfer->addMessage((new MessageTransfer())->setMessage(static::MESSAGE_DELETE_FAILED));

        return $unzerCredentialsResponseTransfer->setIsSuccessful(false);
    }

    /**
     * @param UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return bool
     */
    protected function haveChildCredentials(UnzerCredentialsTransfer $unzerCredentialsTransfer): bool
    {
        $unzerCredentialsType = $unzerCredentialsTransfer->getType();
        if (
            $unzerCredentialsType === UnzerConstants::UNZER_CONFIG_TYPE_STANDARD ||
            $unzerCredentialsType === UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MERCHANT
        ) {
            return false;
        }

        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions(
            (new UnzerCredentialsConditionsTransfer())->addParentId($unzerCredentialsTransfer->getIdUnzerCredentials())
        );
        $childUnzerCredentialsCollectionTransfer = $this->unzerRepository->findUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);

        return $childUnzerCredentialsCollectionTransfer->getUnzerCredentials()->count() !== 0;
    }
}
