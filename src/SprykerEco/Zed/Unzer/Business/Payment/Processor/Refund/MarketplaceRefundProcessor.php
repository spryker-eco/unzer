<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Generated\Shared\Transfer\UnzerRefundItemTransfer;
use Generated\Shared\Transfer\UnzerRefundTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class MarketplaceRefundProcessor implements UnzerRefundProcessorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface
     */
    protected $unzerRefundAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface
     */
    protected $unzerPaymentAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface
     */
    protected $unzerPaymentSaver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface $unzerRefundAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface $unzerPaymentMapper
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface $unzerPaymentSaver
     */
    public function __construct(
        UnzerReaderInterface $unzerReader,
        UnzerRefundAdapterInterface $unzerRefundAdapter,
        UnzerPaymentMapperInterface $unzerPaymentMapper,
        UnzerPaymentAdapterInterface $unzerPaymentAdapter,
        UnzerPaymentSaverInterface $unzerPaymentSaver
    ) {
        $this->unzerReader = $unzerReader;
        $this->unzerRefundAdapter = $unzerRefundAdapter;
        $this->unzerPaymentMapper = $unzerPaymentMapper;
        $this->unzerPaymentAdapter = $unzerPaymentAdapter;
        $this->unzerPaymentSaver = $unzerPaymentSaver;
    }

    /**
     * @inheritDoc
     */
    public function refund(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $paymentUnzerTransfer = $this->unzerReader->getPaymentUnzerByOrderReference($orderTransfer->getOrderReference());
        $unzerRefundTransfers = $this->buildUnzerMarketplaceRefundTransfers($refundTransfer, $paymentUnzerTransfer);

        foreach ($unzerRefundTransfers as $unzerRefundTransfer) {
            $this->unzerRefundAdapter->refundPayment($unzerRefundTransfer);
        }

        $this->savePaymentEntities($paymentUnzerTransfer, $salesOrderItemIds);
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerRefundTransfer[]
     */
    protected function buildUnzerMarketplaceRefundTransfers(
        RefundTransfer $refundTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer
    ): array {
        $refundTransfer = $this->setParticipantIdForRefundItems($refundTransfer);

        //reorder items by participants
        $participantReorderedItems = $this->reorderRefundItemsByParticipant($refundTransfer);

        //build participant-ready refund transfers
        $unzerRefundTransfers = [];
        foreach ($participantReorderedItems as $participantId => $itemTransfers) {
            $unzerRefundTransfer = $this->buildUnzerMarketplaceRefundTransfer(
                $paymentUnzerTransfer,
                $participantId,
                $itemTransfers
            );
            $unzerRefundTransfers[] = $unzerRefundTransfer;
        }

        return $unzerRefundTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param string $participantId
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return \Generated\Shared\Transfer\UnzerRefundTransfer
     */
    protected function buildUnzerMarketplaceRefundTransfer(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        string $participantId,
        array $itemTransfers
    ): UnzerRefundTransfer {
        if (!$paymentUnzerTransfer->getIsAuthorizable()) {
            //Sofort and BankTransfer transactions do not have participantId
            $participantId = null;
        }

        $paymentUnzerTransactionTransfer = $this->unzerReader
            ->getPaymentUnzerTransactionByPaymentIdAndParticipantId(
                $paymentUnzerTransfer->getPaymentId(),
                UnzerConstants::TRANSACTION_TYPE_CHARGE,
                $participantId
            );

        $unzerRefundTransfer = new UnzerRefundTransfer();
        $unzerRefundTransfer->setIsMarketplace(true);
        $unzerRefundTransfer->setOrderId($paymentUnzerTransfer->getOrderId());
        $unzerRefundTransfer->setInvoiceId($paymentUnzerTransfer->getOrderId());
        $unzerRefundTransfer->setPaymentId($paymentUnzerTransfer->getPaymentId());
        $unzerRefundTransfer->setChargeId($paymentUnzerTransactionTransfer->getTransactionId());

        foreach ($itemTransfers as $itemTransfer) {
            $unzerRefundItemTransfer = $this->buildUnzerRefundItemTransfer($itemTransfer);
            $unzerRefundTransfer->addItem($unzerRefundItemTransfer);
        }

        return $unzerRefundTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    protected function setParticipantIdForRefundItems(RefundTransfer $refundTransfer): RefundTransfer
    {
        foreach ($refundTransfer->getItems() as $itemTransfer) {
            $merchantUnzerTransfer = $this->unzerReader->getMerchantUnzerByMerchantReference($itemTransfer->getMerchantReference());
            $itemTransfer->setUnzerParticipantId($merchantUnzerTransfer->getParticipantId());
        }

        return $refundTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param array $salesOrderItemIds
     *
     * @return void
     */
    protected function savePaymentEntities(PaymentUnzerTransfer $paymentUnzerTransfer, array $salesOrderItemIds): void
    {
        $unzerPaymentTransfer = $this->unzerPaymentMapper
            ->mapPaymentUnzerTransferToUnzerPaymentTransfer($paymentUnzerTransfer, new UnzerPaymentTransfer());
        $unzerPaymentTransfer = $this->unzerPaymentAdapter->getPaymentInfo($unzerPaymentTransfer);

        $this->unzerPaymentSaver->savePaymentEntities(
            $unzerPaymentTransfer,
            UnzerConstants::OMS_STATUS_CHARGE_REFUNDED,
            $salesOrderItemIds
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerRefundItemTransfer
     */
    protected function buildUnzerRefundItemTransfer(ItemTransfer $itemTransfer): UnzerRefundItemTransfer
    {
        $unzerRefundItemTransfer = new UnzerRefundItemTransfer();
        $unzerRefundItemTransfer->setParticipantId($itemTransfer->getUnzerParticipantId());
        $unzerRefundItemTransfer->setAmountGross($itemTransfer->getRefundableAmount() / 100);
        $unzerRefundItemTransfer->setBasketItemReferenceId($itemTransfer->getSku());
        $unzerRefundItemTransfer->setQuantity(UnzerConstants::PARTIAL_REFUND_QUANTITY);

        return $unzerRefundItemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     *
     * @return array
     */
    protected function reorderRefundItemsByParticipant(RefundTransfer $refundTransfer): array
    {
        $participants = [];
        foreach ($refundTransfer->getItems() as $itemTransfer) {
            $participants[$itemTransfer->getUnzerParticipantId()][] = $itemTransfer;
        }

        return $participants;
    }
}
