<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge;

use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerShipmentChargeTransfer;
use Generated\Shared\Transfer\UnzerApiChargeResponseTransfer;
use Generated\Shared\Transfer\UnzerChargeTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerChargeAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerCreditCardChargeProcessor implements UnzerChargeProcessorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerChargeAdapterInterface
     */
    protected $unzerChargeAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface
     */
    protected $unzerCredentialsResolver;

    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface
     */
    protected $unzerEntityManager;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface $unzerPaymentMapper
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerChargeAdapterInterface $unzerChargeAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface $unzerCredentialsResolver
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface $unzerRepository
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface $unzerEntityManager
     */
    public function __construct(
        UnzerPaymentMapperInterface $unzerPaymentMapper,
        UnzerChargeAdapterInterface $unzerChargeAdapter,
        UnzerCredentialsResolverInterface $unzerCredentialsResolver,
        UnzerRepositoryInterface $unzerRepository,
        UnzerEntityManagerInterface $unzerEntityManager
    ) {
        $this->unzerPaymentMapper = $unzerPaymentMapper;
        $this->unzerChargeAdapter = $unzerChargeAdapter;
        $this->unzerCredentialsResolver = $unzerCredentialsResolver;
        $this->unzerRepository = $unzerRepository;
        $this->unzerEntityManager = $unzerEntityManager;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return void
     */
    public function charge(OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $paymentUnzerTransfer = $this->unzerRepository->findPaymentUnzerByOrderReference($orderTransfer->getOrderReferenceOrFail());
        if ($paymentUnzerTransfer === null) {
            throw new UnzerException(sprintf('Unzer Payment not found for order reference %s', $orderTransfer->getOrderReferenceOrFail()));
        }

        $unzerPaymentTransfer = $this->unzerPaymentMapper
            ->mapPaymentUnzerTransferToUnzerPaymentTransfer($paymentUnzerTransfer, new UnzerPaymentTransfer());
        $unzerPaymentTransfer->setUnzerKeypair($this->getUnzerKeyPair($unzerPaymentTransfer));

        $paymentUnzerOrderItemCollectionTransfer = $this->unzerRepository
            ->getPaymentUnzerOrderItemCollectionByOrderId($unzerPaymentTransfer->getOrderIdOrFail());

        $itemCollectionTransfer = new ItemCollectionTransfer();
        foreach ($orderTransfer->getItems() as $itemTransfer) {
            if (in_array($itemTransfer->getIdSalesOrderItemOrFail(), $salesOrderItemIds, true)) {
                $itemCollectionTransfer->addItem($itemTransfer);
            }
        }

        $unzerChargeTransfer = $this->createUnzerCharge($unzerPaymentTransfer, $itemCollectionTransfer);
        $unzerChargeTransfer = $this->addExpensesToUnzerChargeTransfer($unzerChargeTransfer, $orderTransfer, $paymentUnzerOrderItemCollectionTransfer, $salesOrderItemIds);

        $unzerApiChargeResponseTransfer = $this->unzerChargeAdapter->chargePartialAuthorizablePayment($unzerPaymentTransfer, $unzerChargeTransfer);
        $this->updatePaymentUnzerOrderItemEntities($paymentUnzerOrderItemCollectionTransfer, $unzerApiChargeResponseTransfer, $salesOrderItemIds);
        $this->createPaymentUnzerShipmentCharges($unzerChargeTransfer, $unzerApiChargeResponseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param \Generated\Shared\Transfer\UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return void
     */
    protected function updatePaymentUnzerOrderItemEntities(
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
        UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer,
        array $salesOrderItemIds
    ): void {
        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItemTransfer) {
            if (in_array($paymentUnzerOrderItemTransfer->getIdSalesOrderItem(), $salesOrderItemIds, true)) {
                $paymentUnzerOrderItemTransfer->setStatus(UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED);
                $paymentUnzerOrderItemTransfer->setChargeId($unzerApiChargeResponseTransfer->getIdOrFail());
                $this->unzerEntityManager->updatePaymentUnzerOrderItemEntity($paymentUnzerOrderItemTransfer);
            }
        }
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    protected function getUnzerKeyPair(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerKeypairTransfer
    {
        $keypairId = $unzerPaymentTransfer->getUnzerKeypairOrFail()->getKeypairIdOrFail();

        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())->addKeypairId($keypairId),
            );

        return $this->unzerCredentialsResolver
            ->resolveUnzerCredentialsByCriteriaTransfer($unzerCredentialsCriteriaTransfer)
            ->getUnzerKeypairOrFail();
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\ItemCollectionTransfer $itemCollectionTransfer
     * @param string|null $authorizeId
     *
     * @return \Generated\Shared\Transfer\UnzerChargeTransfer
     */
    protected function createUnzerCharge(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        ItemCollectionTransfer $itemCollectionTransfer,
        ?string $authorizeId = null
    ): UnzerChargeTransfer {
        $totalAmount = 0;
        foreach ($itemCollectionTransfer->getItems() as $itemTransfer) {
            $totalAmount += $itemTransfer->getSumPriceToPayAggregation();
        }

        return (new UnzerChargeTransfer())
            ->setOrderId($unzerPaymentTransfer->getOrderId())
            ->setPaymentId($unzerPaymentTransfer->getId())
            ->setAuthorizeId($authorizeId)
            ->setInvoiceId($unzerPaymentTransfer->getInvoiceId())
            ->setPaymentReference($unzerPaymentTransfer->getOrderId())
            ->setAmount($totalAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerChargeTransfer $unzerChargeTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return \Generated\Shared\Transfer\UnzerChargeTransfer
     */
    protected function addExpensesToUnzerChargeTransfer(
        UnzerChargeTransfer $unzerChargeTransfer,
        OrderTransfer $orderTransfer,
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
        array $salesOrderItemIds
    ): UnzerChargeTransfer {
        if ($orderTransfer->getExpenses()->count() === 0) {
            return $unzerChargeTransfer;
        }

        $expensesAmount = 0;
        foreach ($orderTransfer->getExpenses() as $expenseTransfer) {
            if (!$this->expenseHasRelatedChargeItems($expenseTransfer, $orderTransfer, $salesOrderItemIds)) {
                continue;
            }

            if ($this->expenseHasAlreadyChargedItems($expenseTransfer, $orderTransfer, $paymentUnzerOrderItemCollectionTransfer)) {
                continue;
            }

            $expensesAmount += $expenseTransfer->getSumPriceToPayAggregationOrFail();
            $unzerChargeTransfer->addChargedSalesShipmentId($expenseTransfer->getShipmentOrFail()->getIdSalesShipmentOrFail());
        }

        return $unzerChargeTransfer->setAmount($unzerChargeTransfer->getAmount() + $expensesAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
     *
     * @return bool
     */
    protected function isPaymentUnzerOrderItemAlreadyCharged(PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer): bool
    {
        return in_array($paymentUnzerOrderItemTransfer->getStatus(), [
            UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED,
            UnzerConstants::OMS_STATUS_CHARGE_REFUNDED,
        ], true);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array<int, array<\Generated\Shared\Transfer\ItemTransfer>>
     */
    protected function groupOrderItemsByIdSalesShipment(OrderTransfer $orderTransfer): array
    {
        $itemTransfersGroupedByIdSalesShipment = [];
        foreach ($orderTransfer->getItems() as $itemTransfer) {
                $idSalesShipment = $itemTransfer->getShipmentOrFail()->getIdSalesShipmentOrFail();
            $itemTransfersGroupedByIdSalesShipment[$idSalesShipment][] = $itemTransfer;
        }

        return $itemTransfersGroupedByIdSalesShipment;
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return bool
     */
    protected function expenseHasRelatedChargeItems(ExpenseTransfer $expenseTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): bool
    {
        foreach ($orderTransfer->getItems() as $itemTransfer) {
            if (
                $itemTransfer->getShipmentOrFail()->getIdSalesShipmentOrFail() === $expenseTransfer->getShipmentOrFail()->getIdSalesShipmentOrFail()
                && in_array($itemTransfer->getIdSalesOrderItemOrFail(), $salesOrderItemIds, true)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     *
     * @return bool
     */
    protected function expenseHasAlreadyChargedItems(
        ExpenseTransfer $expenseTransfer,
        OrderTransfer $orderTransfer,
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
    ): bool {
        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItemTransfer) {
            if (
                $this->isPaymentUnzerOrderItemAlreadyCharged($paymentUnzerOrderItemTransfer)
                && $this->isPaymentUnzerOrderItemRelatedToExpense($paymentUnzerOrderItemTransfer, $expenseTransfer, $orderTransfer)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    protected function isPaymentUnzerOrderItemRelatedToExpense(
        PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer,
        ExpenseTransfer $expenseTransfer,
        OrderTransfer $orderTransfer
    ): bool {
        foreach ($orderTransfer->getItems() as $itemTransfer) {
            if (
                $itemTransfer->getShipmentOrFail()->getIdSalesShipmentOrFail() === $expenseTransfer->getShipmentOrFail()->getIdSalesShipmentOrFail()
                && $paymentUnzerOrderItemTransfer->getIdSalesOrderItemOrFail() === $itemTransfer->getIdSalesOrderItemOrFail()
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerChargeTransfer $unzerChargeTransfer
     * @param \Generated\Shared\Transfer\UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer
     *
     * @return void
     */
    protected function createPaymentUnzerShipmentCharges(
        UnzerChargeTransfer $unzerChargeTransfer,
        UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer
    ): void {
        foreach ($unzerChargeTransfer->getChargedSalesShipmentIds() as $chargedSalesShipmentId) {
            $paymentUnzerShipmentCharge = (new PaymentUnzerShipmentChargeTransfer())
                ->setChargeId($unzerApiChargeResponseTransfer->getIdOrFail())
                ->setIdSalesShipment($chargedSalesShipmentId);

            $this->unzerEntityManager->createPaymentUnzerShipmentCharge($paymentUnzerShipmentCharge);
        }
    }
}
