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
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

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
        UnzerPaymentMapperInterface       $unzerPaymentMapper,
        UnzerChargeAdapterInterface       $unzerChargeAdapter,
        UnzerCredentialsResolverInterface $unzerCredentialsResolver,
        UnzerRepositoryInterface          $unzerRepository,
        UnzerEntityManagerInterface       $unzerEntityManager
    )
    {
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
     * @return void
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
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
        $unzerChargeTransfer = $this->addExpensesToUnzerChargeTransfer($unzerChargeTransfer, $orderTransfer, $paymentUnzerOrderItemCollectionTransfer);

        $unzerApiChargeResponseTransfer = $this->unzerChargeAdapter->chargePartialAuthorizablePayment($unzerPaymentTransfer, $unzerChargeTransfer);
        $this->updatePaymentUnzerOrderItemEntities($paymentUnzerOrderItemCollectionTransfer, $unzerApiChargeResponseTransfer, $salesOrderItemIds);
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
        UnzerApiChargeResponseTransfer          $unzerApiChargeResponseTransfer,
        array                                   $salesOrderItemIds
    ): void
    {
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
        UnzerPaymentTransfer   $unzerPaymentTransfer,
        ItemCollectionTransfer $itemCollectionTransfer,
        ?string                $authorizeId = null
    ): UnzerChargeTransfer
    {
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
     *
     * @return \Generated\Shared\Transfer\UnzerChargeTransfer
     */
    protected function addExpensesToUnzerChargeTransfer(
        UnzerChargeTransfer                     $unzerChargeTransfer,
        OrderTransfer                           $orderTransfer,
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
    ): UnzerChargeTransfer
    {
        if (
            $orderTransfer->getExpenses()->count() === 0
            || $this->getChargedItemsCount($paymentUnzerOrderItemCollectionTransfer) > 0
        ) {
            return $unzerChargeTransfer;
        }

        $expensesAmount = 0;
        foreach ($orderTransfer->getExpenses() as $expenseTransfer) {
            $expensesAmount += $this->getExpensesAmountForOrderExpense($orderTransfer, $expenseTransfer);
        }

        return $unzerChargeTransfer->setAmount($unzerChargeTransfer->getAmount() + $expensesAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return int
     */
    protected function getExpensesAmountForOrderExpense(OrderTransfer $orderTransfer, ExpenseTransfer $expenseTransfer): int
    {
        $expensesAmount = 0;
        foreach ($orderTransfer->getItems() as $itemTransfer) {
            $itemTransferFkSalesExpense = $itemTransfer->getShipmentOrFail()->getMethodOrFail()->getFkSalesExpense();
            $expenseTransferFkSalesExpense = $expenseTransfer->getShipmentOrFail()->getMethodOrFail()->getFkSalesExpense();
            if ($itemTransferFkSalesExpense === $expenseTransferFkSalesExpense) {
                $expensesAmount += $expenseTransfer->getSumGrossPrice();

                return $expensesAmount;
            }
        }

        return $expensesAmount;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     *
     * @return int
     */
    protected function getChargedItemsCount(
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
    ): int
    {
        $counter = 0;
        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItemTransfer) {
            if ($this->isPaymentUnzerOrderItemAlreadyCharged($paymentUnzerOrderItemTransfer)) {
                $counter++;
            }
        }

        return $counter;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer
     *
     * @return bool
     */
    protected function isPaymentUnzerOrderItemAlreadyCharged(PaymentUnzerOrderItemTransfer $paymentUnzerOrderItemTransfer): bool
    {
        return $paymentUnzerOrderItemTransfer->getStatus() === UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED
            || $paymentUnzerOrderItemTransfer->getStatus() === UnzerConstants::OMS_STATUS_CHARGE_REFUNDED;
    }
}
