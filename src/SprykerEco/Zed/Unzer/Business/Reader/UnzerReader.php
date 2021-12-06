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
use Generated\Shared\Transfer\UnzerConfigCollectionTransfer;
use Generated\Shared\Transfer\UnzerConfigConditionsTransfer;
use Generated\Shared\Transfer\UnzerConfigCriteriaTransfer;
use Generated\Shared\Transfer\UnzerConfigTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
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
    public function getPaymentUnzerByPaymentIdAndPublicKey(string $unzerPaymentId, string $publicKey): ?PaymentUnzerTransfer
    {
        $unzerConfigCriteriaTransfer = (new UnzerConfigCriteriaTransfer())->setUnzerConfigConditions(
            (new UnzerConfigConditionsTransfer())->addPublicKey($publicKey),
        );
        $unzerConfigTransferCollection = $this->unzerRepository->findUnzerConfigsByCriteria($unzerConfigCriteriaTransfer);
        if ($unzerConfigTransferCollection->getUnzerConfigs()->count() === 0) {
            return null;
        }

        return $this->unzerRepository->findPaymentUnzerByPaymentIdAndKeypairId($unzerPaymentId, $publicKey);
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
    public function getUnzerCustomerTransferByCustomerTransfer(CustomerTransfer $customerTransfer): ?UnzerCustomerTransfer
    {
        return $this->unzerRepository->findUnzerCustomerByIdCustomer($customerTransfer->getIdCustomer());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerConfigCriteriaTransfer $unzerConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigTransfer|null
     */
    public function getUnzerConfigByCriteria(UnzerConfigCriteriaTransfer $unzerConfigCriteriaTransfer): ?UnzerConfigTransfer
    {
        $unzerConfigCollectionTransfer = $this->unzerRepository->findUnzerConfigsByCriteria($unzerConfigCriteriaTransfer);
        if ($unzerConfigCollectionTransfer->getUnzerConfigs()->count() === 0) {
            return null;
        }

        $unzerConfigTransfer = $unzerConfigCollectionTransfer->getUnzerConfigs()[0];
        $this->attachUnzerKeypairTransfer($unzerConfigTransfer);

        return $unzerConfigTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerConfigCriteriaTransfer $unzerConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigCollectionTransfer
     */
    public function getUnzerConfigCollectionByCriteria(UnzerConfigCriteriaTransfer $unzerConfigCriteriaTransfer): UnzerConfigCollectionTransfer
    {
        $unzerConfigCollectionTransfer = $this->unzerRepository->findUnzerConfigsByCriteria($unzerConfigCriteriaTransfer);
        foreach ($unzerConfigCollectionTransfer->getUnzerConfigs() as $unzerConfigTransfer) {
            $this->attachUnzerKeypairTransfer($unzerConfigTransfer);
        }

        return $unzerConfigCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerConfigCriteriaTransfer $unzerConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigCollectionTransfer
     */
    public function getUnzerConfigsByCriteria(UnzerConfigCriteriaTransfer $unzerConfigCriteriaTransfer): UnzerConfigCollectionTransfer
    {
        return $this->unzerRepository->findUnzerConfigsByCriteria($unzerConfigCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerConfigTransfer $unzerConfigTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigTransfer
     */
    protected function attachUnzerKeypairTransfer(UnzerConfigTransfer $unzerConfigTransfer): UnzerConfigTransfer
    {
        $unzerPrivateKey = $this->unzerVaultReader->retrieveUnzerPrivateKey($unzerConfigTransfer->getKeypairId());
        if ($unzerPrivateKey === null) {
            return $unzerConfigTransfer;
        }

        $unzerKeyPairTransfer = (new UnzerKeypairTransfer())
            ->setPublicKey($unzerConfigTransfer->getPublicKey())
            ->setPrivateKey($unzerPrivateKey)
            ->setKeypairId($unzerConfigTransfer->getKeypairId());

        return $unzerConfigTransfer->setUnzerKeypair($unzerKeyPairTransfer);
    }
}
