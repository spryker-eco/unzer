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
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Generated\Shared\Transfer\UnzerRefundItemTransfer;
use Generated\Shared\Transfer\UnzerRefundTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerMarketplaceRefundProcessor implements UnzerRefundProcessorInterface
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
     * @var \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface
     */
    protected $unzerCredentialsResolver;

    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface $unzerRefundAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface $unzerPaymentMapper
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface $unzerPaymentSaver
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface $unzerCredentialsResolver
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface $salesFacade
     */
    public function __construct(
        UnzerReaderInterface $unzerReader,
        UnzerRefundAdapterInterface $unzerRefundAdapter,
        UnzerPaymentMapperInterface $unzerPaymentMapper,
        UnzerPaymentAdapterInterface $unzerPaymentAdapter,
        UnzerPaymentSaverInterface $unzerPaymentSaver,
        UnzerCredentialsResolverInterface $unzerCredentialsResolver,
        UnzerToSalesFacadeInterface $salesFacade
    ) {
        $this->unzerReader = $unzerReader;
        $this->unzerRefundAdapter = $unzerRefundAdapter;
        $this->unzerPaymentMapper = $unzerPaymentMapper;
        $this->unzerPaymentAdapter = $unzerPaymentAdapter;
        $this->unzerPaymentSaver = $unzerPaymentSaver;
        $this->unzerCredentialsResolver = $unzerCredentialsResolver;
        $this->salesFacade = $salesFacade;
    }

    /**
     * @inheritDoc
     */
    public function refund(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $paymentUnzerTransfer = $this->unzerReader->getPaymentUnzerByOrderReference($orderTransfer->getOrderReference());
        $unzerRefundTransfers = $this->createUnzerMarketplaceRefundTransfers($refundTransfer, $paymentUnzerTransfer, $orderTransfer);
        $unzerKeypairTransfer = $this->getUnzerKeypairTransfer($paymentUnzerTransfer->getKeypairId());

        foreach ($unzerRefundTransfers as $unzerRefundTransfer) {
            $this->unzerRefundAdapter->refundPayment(
                $unzerRefundTransfer,
                $unzerKeypairTransfer,
            );
        }

        $this->saveUnzerPaymentDetails(
            $paymentUnzerTransfer,
            $unzerKeypairTransfer,
            $salesOrderItemIds,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    protected function createUnzerMarketplaceRefundTransfers(
        RefundTransfer $refundTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer,
        OrderTransfer $orderTransfer
    ): array {
        $this->setParticipantIdForOrderAndRefundItems($orderTransfer, $refundTransfer);
        $participantReorderedItems = $this->reorderRefundItemsByParticipant($refundTransfer);

        $unzerRefundTransfers = [];
        foreach ($participantReorderedItems as $participantId => $itemTransfers) {
            $unzerRefundTransfers[] = $this->createUnzerRefundTransfer(
                $paymentUnzerTransfer,
                $participantId,
                $itemTransfers,
                $orderTransfer,
            );
        }

        return $unzerRefundTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param string $participantId
     * @param array $itemTransfers
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerRefundTransfer
     */
    protected function createUnzerRefundTransfer(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        string $participantId,
        array $itemTransfers,
        OrderTransfer $orderTransfer
    ): UnzerRefundTransfer {
        if (!$paymentUnzerTransfer->getIsAuthorizable()) {
            //Sofort and BankTransfer transactions do not have participantId
            $participantId = null;
        }

        $paymentUnzerTransactionTransfer = $this->unzerReader
            ->getPaymentUnzerTransactionByPaymentIdAndParticipantId(
                $paymentUnzerTransfer->getPaymentIdOrFail(),
                UnzerConstants::TRANSACTION_TYPE_CHARGE,
                $participantId,
            );

        $unzerRefundTransfer = (new UnzerRefundTransfer())
            ->setIsMarketplace(true)
            ->setOrderId($paymentUnzerTransfer->getOrderId())
            ->setInvoiceId($paymentUnzerTransfer->getOrderId())
            ->setPaymentId($paymentUnzerTransfer->getPaymentId())
            ->setChargeId($paymentUnzerTransactionTransfer->getTransactionId());

        foreach ($itemTransfers as $itemTransfer) {
            $unzerRefundItemTransfer = $this->createUnzerRefundItemTransfer($itemTransfer);
            $unzerRefundTransfer->addItem($unzerRefundItemTransfer);
        }

        $this->addShipmentRefund(
            $unzerRefundTransfer,
            $itemTransfers,
            $orderTransfer,
        );

        return $unzerRefundTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     *
     * @return void
     */
    protected function setParticipantIdForOrderAndRefundItems(OrderTransfer $orderTransfer, RefundTransfer $refundTransfer): void
    {
        foreach ($orderTransfer->getItems() as $orderItemTransfer) {
            $paymentUnzerOrderItemTransfer = $this->unzerReader->getPaymentUnzerOrderItemByIdSalesOrderItem($orderItemTransfer->getIdSalesOrderItem());
            if ($paymentUnzerOrderItemTransfer->getParticipantId() === null) {
                continue;
            }
            $orderItemTransfer->setUnzerParticipantId($paymentUnzerOrderItemTransfer->getParticipantId());

            foreach ($refundTransfer->getItems() as $refundItemTransfer) {
                if ($refundItemTransfer->getIdSalesOrderItem() === $orderItemTransfer->getIdSalesOrderItem()) {
                    $refundItemTransfer->setUnzerParticipantId($paymentUnzerOrderItemTransfer->getParticipantId());
                }
            }
        }
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     * @param array $salesOrderItemIds
     *
     * @return void
     */
    protected function saveUnzerPaymentDetails(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer,
        array $salesOrderItemIds
    ): void {
        $unzerPaymentTransfer = $this->unzerPaymentMapper
            ->mapPaymentUnzerTransferToUnzerPaymentTransfer($paymentUnzerTransfer, new UnzerPaymentTransfer());
        $unzerPaymentTransfer->setUnzerKeypair($unzerKeypairTransfer);
        $unzerPaymentTransfer = $this->unzerPaymentAdapter->getPaymentInfo($unzerPaymentTransfer);

        $this->unzerPaymentSaver->saveUnzerPaymentDetails(
            $unzerPaymentTransfer,
            UnzerConstants::OMS_STATUS_CHARGE_REFUNDED,
            $salesOrderItemIds,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerRefundItemTransfer
     */
    protected function createUnzerRefundItemTransfer(ItemTransfer $itemTransfer): UnzerRefundItemTransfer
    {
        return (new UnzerRefundItemTransfer())
            ->setParticipantId($itemTransfer->getUnzerParticipantId())
            ->setAmountGross((float)$itemTransfer->getRefundableAmount() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setBasketItemReferenceId($itemTransfer->getSku())
            ->setQuantity(UnzerConstants::PARTIAL_REFUND_QUANTITY);
    }

    /**
     * @param string $keypair
     *
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    protected function getUnzerKeypairTransfer(string $keypair): UnzerKeypairTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())->addKeypairId($keypair),
            );

        $unzerCredentialsTransfer = $this->unzerCredentialsResolver->resolveUnzerCredentialsByCriteriaTransfer($unzerCredentialsCriteriaTransfer);

        return $unzerCredentialsTransfer->getUnzerKeypair();
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
            $participants[$itemTransfer->getUnzerParticipantIdOrFail()][] = $itemTransfer;
        }

        return $participants;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerRefundTransfer $unzerRefundTransfer
     * @param array $itemTransfers
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    protected function addShipmentRefund(
        UnzerRefundTransfer $unzerRefundTransfer,
        array $itemTransfers,
        OrderTransfer $orderTransfer
    ): void {
        if (UnzerConstants::UNZER_SHIPMENT_REFUND_STRATEGY_LIST[UnzerConstants::UNZER_SHIPMENT_REFUND_STRATEGY] === UnzerConstants::SHIPMENT_REFUND_WITH_LAST_ORDER_ITEM_REFUND) {
            $this->addShipemtRefundWithLastOrderItem($unzerRefundTransfer, $orderTransfer, $itemTransfers);
        }

        if (UnzerConstants::UNZER_SHIPMENT_REFUND_STRATEGY_LIST[UnzerConstants::UNZER_SHIPMENT_REFUND_STRATEGY] === UnzerConstants::SHIPMENT_REFUND_WITH_LAST_SHIPMENT_ITEM_REFUND) {
            $this->addShipemtRefundWithLastShipmentItem($unzerRefundTransfer, $orderTransfer, $itemTransfers);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerRefundTransfer $unzerRefundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array $itemsToRefundTransfers
     *
     * @return void
     */
    protected function addShipemtRefundWithLastOrderItem(
        UnzerRefundTransfer $unzerRefundTransfer,
        OrderTransfer $orderTransfer,
        array $itemsToRefundTransfers
    ): void {
        $unrefundedUnzerOrderItems = $this->unzerReader
            ->getUnrefundedPaymentUnzerOrderItemCollectionByOrderReference($orderTransfer->getOrderReference())
            ->getPaymentUnzerOrderItems();

        if (count($unrefundedUnzerOrderItems) === count($itemsToRefundTransfers)) {
            $sortedUnrefundedOrderItemIds = $this->getItemsIds($unrefundedUnzerOrderItems);
            $sortedOrderItemsToRefundIds = $this->getItemsIds($itemsToRefundTransfers);

            if (sort($sortedUnrefundedOrderItemIds) === sort($sortedOrderItemsToRefundIds)) {
                $shipmentRefundData = $this->getOrderShipmentsRefundData($orderTransfer);
                foreach ($shipmentRefundData as $participantId => $amount) {
                    $unzerRefundItemTransfer = $this->createUnzerShipmentRefundItemTransfer($participantId, $amount);
                    $unzerRefundTransfer->addItem($unzerRefundItemTransfer);
                }
            }
        }
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerRefundTransfer $unzerRefundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array $itemsToRefundTransfers
     *
     * @return void
     */
    protected function addShipemtRefundWithLastShipmentItem(
        UnzerRefundTransfer $unzerRefundTransfer,
        OrderTransfer $orderTransfer,
        array $itemsToRefundTransfers
    ): void {
        $unrefundedUnzerOrderItemIds = $this->getUnrefundedUnzerOrderItemsIds($orderTransfer->getOrderReference());

        $itemsToRefundIds = $this->getItemsIds($itemsToRefundTransfers);

        $unrefundedSalesOrderItems = array_filter(
            $orderTransfer->getItems()->getArrayCopy(),
            function ($orderItem) use ($unrefundedUnzerOrderItemIds) {
                return in_array($orderItem->getIdSalesOrderItem(), $unrefundedUnzerOrderItemIds);
            },
        );
        $unrefundedShipmentToOrderItemMap = [];
        /** @var \Generated\Shared\Transfer\ItemTransfer $salesOrderItemTransfer */
        foreach ($unrefundedSalesOrderItems as $salesOrderItemTransfer) {
            $unrefundedShipmentToOrderItemMap[$salesOrderItemTransfer->getShipmentOrFail()->getIdSalesShipment()][] = $salesOrderItemTransfer->getIdSalesOrderItem();
        }

        foreach ($unrefundedShipmentToOrderItemMap as $idSalesShipment => $orderItemIds) {
            if (!array_diff($orderItemIds, $itemsToRefundIds)) {
                $shipmentRefundData = $this->getOrderShipmentsRefundData($orderTransfer, $idSalesShipment);
                foreach ($shipmentRefundData as $participantId => $amount) {
                    $unzerRefundItemTransfer = $this->createUnzerShipmentRefundItemTransfer($participantId, $amount);
                    $unzerRefundTransfer->addItem($unzerRefundItemTransfer);
                }
            }
        }
    }

    /**
     * @param string $participantId
     * @param int $amount
     *
     * @return \Generated\Shared\Transfer\UnzerRefundItemTransfer
     */
    protected function createUnzerShipmentRefundItemTransfer(string $participantId, int $amount): UnzerRefundItemTransfer
    {
        return (new UnzerRefundItemTransfer())
            ->setParticipantId($participantId)
            ->setAmountGross($amount / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setBasketItemReferenceId(sprintf(UnzerConstants::UNZER_MARKETPLACE_BASKET_SHIPMENT_REFERENCE_ID, $participantId))
            ->setQuantity(UnzerConstants::PARTIAL_REFUND_QUANTITY);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int|null $idSalesShipment
     *
     * @return array
     */
    protected function getOrderShipmentsRefundData(
        OrderTransfer $orderTransfer,
        ?int $idSalesShipment = null
    ): array {
        $shipments = [];

        foreach ($orderTransfer->getExpenses() as $expenseTransferToRefund) {
            if (
                $idSalesShipment !== null
                && $expenseTransferToRefund->getShipmentOrFail()->getIdSalesShipment() !== $idSalesShipment
            ) {
                continue;
            }

            $refundableAmount = (int)$expenseTransferToRefund->getRefundableAmount() - (int)$expenseTransferToRefund->getCanceledAmount();
            if ($refundableAmount !== 0) {
                /** @var \Generated\Shared\Transfer\ItemTransfer $orderItem */
                foreach ($orderTransfer->getItems()->getArrayCopy() as $orderItem) {
                    if ($expenseTransferToRefund->getMerchantReference() === $orderItem->getMerchantReference()) {
                        if (isset($shipments[$orderItem->getUnzerParticipantId()])) {
                            $shipments[$orderItem->getUnzerParticipantId()] += $refundableAmount;
                        } else {
                            $shipments[$orderItem->getUnzerParticipantId()] = $refundableAmount;
                        }

                        break;
                    }
                }
            }
        }

        return $shipments;
    }

    /**
     * @param string $orderReference
     *
     * @return array
     */
    protected function getUnrefundedUnzerOrderItemsIds(string $orderReference): array
    {
        return array_map(
            function ($orderItem) {
                return $orderItem->getIdSalesOrderItem();
            },
            $this->unzerReader
                ->getUnrefundedPaymentUnzerOrderItemCollectionByOrderReference($orderReference)
                ->getPaymentUnzerOrderItems()
                ->getArrayCopy(),
        );
    }

    /**
     * @param array $itemsTransfers
     *
     * @return array
     */
    protected function getItemsIds(array $itemsTransfers): array
    {
        return array_map(
            function ($itemTransfer) {
                return $itemTransfer->getIdSalesOrderItem();
            },
            $itemsTransfers,
        );
    }
}
