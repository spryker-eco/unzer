<?php

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionConditionsTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCriteriaTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Generated\Shared\Transfer\UnzerRefundTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerRefundAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Mapper\UnzerMarketplaceRefundMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface;
use SprykerEco\Zed\Unzer\Business\Refund\RefundStrategy\ExpensesRefundStrategyResolver\UnzerExpensesRefundStrategyResolverInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerRefundProcessor implements UnzerRefundProcessorInterface
{
    /**
     * @var UnzerCredentialsResolverInterface
     */
    protected $unzerCredentialsResolver;

    /**
     * @var UnzerExpensesRefundStrategyResolverInterface
     */
    protected $unzerExpensesRefundStrategyResolver;

    /**
     * @var UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var UnzerRefundAdapterInterface
     */
    protected $unzerRefundAdapter;

    /**
     * @var UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @var UnzerMarketplaceRefundMapperInterface
     */
    protected $unzerRefundMapper;

    /**
     * @var UnzerPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @var UnzerPaymentAdapterInterface
     */
    protected $unzerPaymentAdapter;

    /**
     * @var UnzerPaymentSaverInterface
     */
    protected $unzerPaymentSaver;

    /**
     * @param UnzerCredentialsResolverInterface $unzerCredentialsResolver
     * @param UnzerExpensesRefundStrategyResolverInterface $unzerExpensesRefundStrategyResolver
     * @param UnzerConfig $unzerConfig
     * @param UnzerRefundAdapterInterface $unzerRefundAdapter
     * @param UnzerRepositoryInterface $unzerRepository
     * @param UnzerPaymentMapperInterface $unzerPaymentMapper
     * @param UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param UnzerPaymentSaverInterface $unzerPaymentSaver
     */
    public function __construct(
        UnzerCredentialsResolverInterface            $unzerCredentialsResolver,
        UnzerExpensesRefundStrategyResolverInterface $unzerExpensesRefundStrategyResolver,
        UnzerConfig                                  $unzerConfig,
        UnzerRefundAdapterInterface                  $unzerRefundAdapter,
        UnzerRepositoryInterface                     $unzerRepository,
        UnzerPaymentMapperInterface                  $unzerPaymentMapper,
        UnzerPaymentAdapterInterface                 $unzerPaymentAdapter,
        UnzerPaymentSaverInterface                   $unzerPaymentSaver
    )
    {
        $this->unzerCredentialsResolver = $unzerCredentialsResolver;
        $this->unzerExpensesRefundStrategyResolver = $unzerExpensesRefundStrategyResolver;
        $this->unzerConfig = $unzerConfig;
        $this->unzerRefundAdapter = $unzerRefundAdapter;
        $this->unzerRepository = $unzerRepository;
        $this->unzerPaymentMapper = $unzerPaymentMapper;
        $this->unzerPaymentAdapter = $unzerPaymentAdapter;
        $this->unzerPaymentSaver = $unzerPaymentSaver;
    }

    public function refund(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): void
    {
        $paymentUnzerTransfer = $this->unzerRepository->findPaymentUnzerByOrderReference($orderTransfer->getOrderReference());
        if ($paymentUnzerTransfer === null) {
            throw new UnzerException(sprintf('Unzer payment for order reference %s not found.', $orderTransfer->getOrderReference()));
        }

        $chargeId = $this->getUnzerPaymentChargeId($paymentUnzerTransfer);

        $refundTransfer->addUnzerRefund($this->createUnzerRefund($paymentUnzerTransfer, $refundTransfer, $chargeId));

        $refundTransfer = $this->applyExpensesRefundStrategy($refundTransfer, $orderTransfer, $salesOrderItemIds);
        $this->applyRefundChanges($paymentUnzerTransfer, $refundTransfer, $salesOrderItemIds);
    }

    /**
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return string
     */
    protected function getUnzerPaymentChargeId(PaymentUnzerTransfer $paymentUnzerTransfer): string
    {
        $paymentUnzerTransactionCriteriaTransfer = (new PaymentUnzerTransactionCriteriaTransfer())
            ->setPaymentUnzerTransactionConditions(
                (new PaymentUnzerTransactionConditionsTransfer())
                    ->addFkPaymentUnzerId($paymentUnzerTransfer->getIdPaymentUnzer())
                    ->addType(UnzerConstants::TRANSACTION_TYPE_CHARGE)
                    ->addStatus(UnzerConstants::TRANSACTION_STATUS_SUCCESS)
            );

        $paymentUnzerTransactionCollectionTransfer = $this->unzerRepository
            ->findPaymentUnzerTransactionCollectionByCriteria($paymentUnzerTransactionCriteriaTransfer);

        if ($paymentUnzerTransactionCollectionTransfer->getPaymentUnzerTransactions()->count() === 0) {
            throw new UnzerException(sprintf('Unzer transactions for Payment ID %s not found.', $paymentUnzerTransfer->getPaymentIdOrFail()));
        }

        $paymentUnzerTransaction = $paymentUnzerTransactionCollectionTransfer->getPaymentUnzerTransactions()->getIterator()->current();

        return $paymentUnzerTransaction->getTransactionIdOrFail();
    }

    /**
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     * @param RefundTransfer $refundTransfer
     * @param string $chargeId
     *
     * @return UnzerRefundTransfer
     */
    protected function createUnzerRefund(
        PaymentUnzerTransfer   $paymentUnzerTransfer,
        RefundTransfer $refundTransfer,
        string                 $chargeId
    ): UnzerRefundTransfer
    {
        $refundAmount = 0;
        foreach ($refundTransfer->getItems() as $itemTransfer) {
            $refundAmount += $itemTransfer->getRefundableAmountOrFail();
        }

        return (new UnzerRefundTransfer())
            ->setPaymentId($paymentUnzerTransfer->getPaymentIdOrFail())
            ->setChargeId($chargeId)
            ->setAmount($refundAmount / UnzerConstants::INT_TO_FLOAT_DIVIDER);
    }

    /**
     * @param RefundTransfer $refundTransfer
     * @param OrderTransfer $orderTransfer
     * @param array $salesOrderItemIds
     *
     * @return RefundTransfer
     */
    protected function applyExpensesRefundStrategy(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItemIds): RefundTransfer
    {
        $unzerExpensesRefundStrategy = $this->unzerExpensesRefundStrategyResolver->resolveRefundStrategy($this->unzerConfig->getExpensesRefundStrategyKey());

        return $unzerExpensesRefundStrategy->prepareUnzerRefund($refundTransfer, $orderTransfer, $salesOrderItemIds);
    }

    protected function applyRefundChanges(PaymentUnzerTransfer $paymentUnzerTransfer, $refundTransfer, array $salesOrderItemIds)
    {
        $unzerKeypairTransfer = $this->getUnzerKeypair($paymentUnzerTransfer->getKeypairIdOrFail());

        foreach ($refundTransfer->getUnzerRefunds() as $unzerRefundTransfer) {
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
     * @param string $keypairId
     *
     * @return UnzerKeypairTransfer
     */
    protected function getUnzerKeypair(string $keypairId): UnzerKeypairTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())->addKeypairId($keypairId)
            );
        $unzerCredentialsTransfer = $this->unzerCredentialsResolver
            ->resolveUnzerCredentialsByCriteriaTransfer($unzerCredentialsCriteriaTransfer);

        return $unzerCredentialsTransfer->getUnzerKeypairOrFail();
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
        array                $salesOrderItemIds
    ): void
    {
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
}
