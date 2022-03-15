<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Reader;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;

class UnzerReader implements UnzerReaderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerVaultReaderInterface
     */
    protected $unzerVaultReader;

    /**
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface $unzerRepository
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerVaultReaderInterface $unzerVaultReader
     */
    public function __construct(
        UnzerRepositoryInterface $unzerRepository,
        UnzerVaultReaderInterface $unzerVaultReader
    ) {
        $this->unzerRepository = $unzerRepository;
        $this->unzerVaultReader = $unzerVaultReader;
    }

    /**
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    public function getPaymentUnzerByOrderReference(string $orderReference): PaymentUnzerTransfer
    {
        return $this->unzerRepository->findPaymentUnzerByOrderReference($orderReference) ?? new PaymentUnzerTransfer();
    }

    /**
     * @param string $orderId
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer
     */
    public function getPaymentUnzerOrderItemCollectionByOrderId(string $orderId): PaymentUnzerOrderItemCollectionTransfer
    {
        return $this->unzerRepository->getPaymentUnzerOrderItemCollectionByOrderId($orderId);
    }

    /**
     * @param string $unzerPaymentId
     * @param string $publicKey
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer|null
     */
    public function findPaymentUnzerByPaymentIdAndPublicKey(string $unzerPaymentId, string $publicKey): ?PaymentUnzerTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions(
            (new UnzerCredentialsConditionsTransfer())->addPublicKey($publicKey),
        );
        $unzerCredentialsCollectionTransfer = $this->unzerRepository->findUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsCollectionTransfer->getUnzerCredentials()->count() !== 1) {
            return null;
        }
        $unzerCredentialsTransfer = $unzerCredentialsCollectionTransfer->getUnzerCredentials()[0];

        return $this->unzerRepository->findPaymentUnzerByPaymentIdAndKeypairId(
            $unzerPaymentId,
            $unzerCredentialsTransfer->getKeypairId(),
        );
    }

    /**
     * @param int $idSalesOrderItem
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer
     */
    public function getPaymentUnzerOrderItemByIdSalesOrderItem(int $idSalesOrderItem): PaymentUnzerOrderItemTransfer
    {
        return $this->unzerRepository
                ->findPaymentUnzerOrderItemByIdSalesOrderItem($idSalesOrderItem) ?? new PaymentUnzerOrderItemTransfer();
    }

    /**
     * @param string $paymentId
     * @param string $transactionType
     * @param string|null $participantId
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer
     */
    public function getPaymentUnzerTransactionByPaymentIdAndParticipantId(
        string $paymentId,
        string $transactionType,
        ?string $participantId = null
    ): PaymentUnzerTransactionTransfer {
        return $this->unzerRepository
                ->findPaymentUnzerTransactionByPaymentIdAndParticipantId($paymentId, $transactionType, $participantId)
            ?? new PaymentUnzerTransactionTransfer();
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer|null
     */
    public function findUnzerCustomerTransferByCustomerTransfer(CustomerTransfer $customerTransfer): ?UnzerCustomerTransfer
    {
        return $this->unzerRepository->findUnzerCustomerByIdCustomer($customerTransfer->getIdCustomerOrFail());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer|null
     */
    public function findUnzerCredentialsByCriteria(UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer): ?UnzerCredentialsTransfer
    {
        $unzerCredentialsCollectionTransfer = $this->unzerRepository->findUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsCollectionTransfer->getUnzerCredentials()->count() !== 1) {
            return null;
        }

        $unzerCredentialsTransfer = $unzerCredentialsCollectionTransfer->getUnzerCredentials()[0];
        $this->attachUnzerPrivateKey($unzerCredentialsTransfer);

        return $unzerCredentialsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer
     */
    public function getUnzerCredentialsCollectionByCriteria(
        UnzerCredentialsCriteriaTransfer $unzerCredentialsCriteriaTransfer
    ): UnzerCredentialsCollectionTransfer {
        $unzerCredentialsCollectionTransfer = $this->unzerRepository->findUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);
        foreach ($unzerCredentialsCollectionTransfer->getUnzerCredentials() as $unzerCredentialsTransfer) {
            $this->attachUnzerPrivateKey($unzerCredentialsTransfer);
        }

        return $unzerCredentialsCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    protected function attachUnzerPrivateKey(UnzerCredentialsTransfer $unzerCredentialsTransfer): UnzerCredentialsTransfer
    {
        $unzerPrivateKey = $this->unzerVaultReader->retrieveUnzerPrivateKey($unzerCredentialsTransfer->getKeypairIdOrFail());
        if ($unzerPrivateKey === null) {
            return $unzerCredentialsTransfer;
        }

        $unzerKeyPairTransfer = $unzerCredentialsTransfer->getUnzerKeypairOrFail();
        $unzerKeyPairTransfer->setPrivateKey($unzerPrivateKey);

        return $unzerCredentialsTransfer->setUnzerKeypair($unzerKeyPairTransfer);
    }
}
