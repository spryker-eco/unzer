<?php

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\Charge;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\UnzerChargeTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerChargeAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerCreditCardChargeProcessor implements UnzerChargeProcessorInterface
{
    /**
     * @var UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var UnzerPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @var UnzerChargeAdapterInterface
     */
    protected $unzerChargeAdapter;

    /**
     * @var UnzerCredentialsResolverInterface
     */
    protected $unzerCredentialsResolver;

    /**
     * @var UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @var UnzerEntityManagerInterface
     */
    protected $unzerEntityManager;

    /**
     * @param UnzerPaymentMapperInterface $unzerPaymentMapper
     * @param UnzerChargeAdapterInterface $unzerChargeAdapter
     * @param UnzerCredentialsResolverInterface $unzerCredentialsResolver
     * @param UnzerRepositoryInterface $unzerRepository
     * @param UnzerEntityManagerInterface $unzerEntityManager
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
     * @param OrderTransfer $orderTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @throws UnzerException
     */
    public function charge(OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $paymentUnzerTransfer = $this->unzerRepository->findPaymentUnzerByOrderReference($orderTransfer->getOrderReference());
        if ($paymentUnzerTransfer === null) {
            throw new UnzerException('Unzer Payment not found for order reference: ' . $orderTransfer->getOrderReference());
        }

        $unzerPaymentTransfer = $this->unzerPaymentMapper
            ->mapPaymentUnzerTransferToUnzerPaymentTransfer($paymentUnzerTransfer, new UnzerPaymentTransfer());
        $unzerPaymentTransfer->setUnzerKeypair($this->getUnzerKeyPair($unzerPaymentTransfer));

        $paymentUnzerOrderItemCollectionTransfer = $this->unzerRepository
            ->getPaymentUnzerOrderItemCollectionByOrderId($unzerPaymentTransfer->getOrderId());

        $itemCollectionTransfer = new ItemCollectionTransfer();
        foreach ($orderTransfer->getItems() as $itemTransfer) {
            $itemCollectionTransfer->addItem($itemTransfer);
        }

        $unzerChargeTransfer = $this->createUnzerCharge($unzerPaymentTransfer, $itemCollectionTransfer);
        $this->addExpensesToUnzerChargeTransfer($unzerChargeTransfer, $orderTransfer, $paymentUnzerOrderItemCollectionTransfer);

        $this->unzerChargeAdapter->chargePartialAuthorizablePayment($unzerPaymentTransfer, $unzerChargeTransfer);
        $this->updatePaymentUnzerOrderItemEntities($paymentUnzerOrderItemCollectionTransfer, $salesOrderItemIds);
    }

    /**
     * @param PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param array<int> $salesOrderItemIds
     */
    protected function updatePaymentUnzerOrderItemEntities(PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer, array $salesOrderItemIds): void
    {
        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItemTransfer) {
            if (in_array($paymentUnzerOrderItemTransfer->getIdSalesOrderItem(), $salesOrderItemIds, true)) {
                $paymentUnzerOrderItemTransfer->setStatus(UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED);
                $this->unzerEntityManager->savePaymentUnzerOrderItemEntity($paymentUnzerOrderItemTransfer);
            }
        }
    }

    /**
     * @param UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return UnzerKeypairTransfer
     *
     * @throws UnzerException
     */
    protected function getUnzerKeyPair(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerKeypairTransfer
    {
        $keypairId = $unzerPaymentTransfer->getUnzerKeypairOrFail()->getKeypairId();

        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())->addKeypairId($keypairId)
            );

        $unzerCredentialsTransfer = $this->unzerCredentialsResolver
            ->resolveUnzerCredentialsByCriteriaTransfer($unzerCredentialsCriteriaTransfer);

        if ($unzerCredentialsTransfer === null) {
            throw new UnzerException('UnzerCredentials not found by keypairId: ' . $keypairId);
        }

        return $unzerCredentialsTransfer->getUnzerKeypairOrFail();
    }

    /**
     * @param UnzerPaymentTransfer $unzerPaymentTransfer
     * @param ItemCollectionTransfer $itemCollectionTransfer
     * @param string|null $authorizeId
     *
     * @return UnzerChargeTransfer
     */
    protected function createUnzerCharge(UnzerPaymentTransfer $unzerPaymentTransfer, ItemCollectionTransfer $itemCollectionTransfer, string $authorizeId = null): UnzerChargeTransfer
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
     * @param UnzerChargeTransfer $unzerChargeTransfer
     * @param OrderTransfer $orderTransfer
     * @param PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     *
     * @return UnzerChargeTransfer
     */
    protected function addExpensesToUnzerChargeTransfer(
        UnzerChargeTransfer                     $unzerChargeTransfer,
        OrderTransfer                           $orderTransfer,
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
    ): UnzerChargeTransfer
    {
        if ($orderTransfer->getExpenses()->count() === 0) {
            return $unzerChargeTransfer;
        }

        if ($this->countItemsCharged($paymentUnzerOrderItemCollectionTransfer) > 0) {
            return $unzerChargeTransfer;
        }

        $expensesAmount = 0;
        foreach ($orderTransfer->getExpenses() as $expenseTransfer) {
            foreach ($orderTransfer->getItems() as $itemTransfer) {
                $itemTransferFkSalesExpense = $itemTransfer->getShipmentOrFail()->getMethodOrFail()->getFkSalesExpense();
                $expenseTransferFkSalesExpense = $expenseTransfer->getShipmentOrFail()->getMethodOrFail()->getFkSalesExpense();
                if ($itemTransferFkSalesExpense === $expenseTransferFkSalesExpense) {
                    $expensesAmount += $expenseTransfer->getSumGrossPrice();

                    break;
                }
            }
        }

        return $unzerChargeTransfer->setAmount($unzerChargeTransfer->getAmount() + $expensesAmount);
    }

    /**
     * @param PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     *
     * @return int
     */
    protected function countItemsCharged(
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
    ): int
    {
        $counter = 0;
        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            if ($paymentUnzerOrderItem->getStatus() === UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED) {
                $counter++;
            }
        }

        return $counter;
    }
}
