<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Reader;

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
     * @param string $merchantReference
     *
     * @return \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer
     */
    public function getMerchantUnzerByMerchantReference(string $merchantReference): MerchantUnzerParticipantTransfer
    {
        return $this
                ->unzerRepository
                ->findMerchantUnzerParticipantByMerchantReference($merchantReference) ?? new MerchantUnzerParticipantTransfer();
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
    public function getPaymentUnzerOrderItemCollectionByOrderReference(string $orderId): PaymentUnzerOrderItemCollectionTransfer
    {
        return $this->unzerRepository->findPaymentUnzerOrderItemCollectionByOrderId($orderId);
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
