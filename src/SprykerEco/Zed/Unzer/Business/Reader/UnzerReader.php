<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Reader;

use Generated\Shared\Transfer\MerchantUnzerParticipantCollectionTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;

class UnzerReader implements UnzerReaderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface $unzerRepository
     */
    public function __construct(UnzerRepositoryInterface $unzerRepository)
    {
        $this->unzerRepository = $unzerRepository;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantUnzerParticipantCriteriaTransfer $merchantUnzerParticipantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer|null
     */
    public function getMerchantUnzerParticipantByCriteria(
        MerchantUnzerParticipantCriteriaTransfer $merchantUnzerParticipantCriteriaTransfer
    ): ?MerchantUnzerParticipantTransfer {
        $merchantUnzerParticipantCollectionTransfer = $this->unzerRepository
                ->findMerchantUnzerParticipantByCriteria($merchantUnzerParticipantCriteriaTransfer);

        if ($merchantUnzerParticipantCollectionTransfer->getMerchantUnzerParticipants()->count() === 1) {
            return $merchantUnzerParticipantCollectionTransfer->getMerchantUnzerParticipants()[0];
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantUnzerParticipantCriteriaTransfer $merchantUnzerParticipantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUnzerParticipantCollectionTransfer
     */
    public function getMerchantUnzerParticipantCollectionByCriteria(
        MerchantUnzerParticipantCriteriaTransfer $merchantUnzerParticipantCriteriaTransfer
    ): MerchantUnzerParticipantCollectionTransfer {
        return $this->unzerRepository
            ->findMerchantUnzerParticipantByCriteria($merchantUnzerParticipantCriteriaTransfer);
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
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    public function getPaymentUnzerByPaymentId(string $unzerPaymentId): PaymentUnzerTransfer
    {
        return $this->unzerRepository->findPaymentUnzerByPaymentId($unzerPaymentId) ?? new PaymentUnzerTransfer();
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
}
