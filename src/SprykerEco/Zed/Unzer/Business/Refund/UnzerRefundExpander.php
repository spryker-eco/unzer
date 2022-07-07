<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Refund;

use ArrayObject;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerRefundItemTransfer;
use Generated\Shared\Transfer\UnzerRefundTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants as UnzerSharedConstants;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerRefundExpander implements UnzerRefundExpanderInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface $unzerRepository
     */
    public function __construct(
        UnzerReaderInterface $unzerReader,
        UnzerRepositoryInterface $unzerRepository
    ) {
        $this->unzerReader = $unzerReader;
        $this->unzerRepository = $unzerRepository;
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfersCollectionForRefund
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    public function expandRefundWithUnzerRefundCollection(
        RefundTransfer $refundTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer,
        ArrayObject $expenseTransfersCollectionForRefund
    ): RefundTransfer {
        $refundTransfer = $this->addExpenseRefunds($refundTransfer, $expenseTransfersCollectionForRefund);

        if ($paymentUnzerTransfer->getIsMarketplace()) {
            return $this->processMarketplaceExpensesRefund($refundTransfer, $paymentUnzerTransfer, $expenseTransfersCollectionForRefund);
        }

        return $this->processStandardExpensesRefund($refundTransfer, $paymentUnzerTransfer, $expenseTransfersCollectionForRefund);
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfersCollectionForRefund
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    protected function processMarketplaceExpensesRefund(
        RefundTransfer $refundTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer,
        ArrayObject $expenseTransfersCollectionForRefund
    ): RefundTransfer {
        $expenseTransfersCollectionForRefund = $this->expandExpensesWithParticipantIds($expenseTransfersCollectionForRefund, $paymentUnzerTransfer);
        $expenseTransfersCollectionForRefund = $this->expandMarketplaceExpensesWithChargeIds($expenseTransfersCollectionForRefund, $paymentUnzerTransfer);
        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            $refundTransfer = $this->expandUnzerRefundTransfersWithExpenseTransfers($refundTransfer, $expenseTransfer);
        }

        return $refundTransfer;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfersCollectionForRefund
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \ArrayObject
     */
    protected function expandExpensesWithParticipantIds(
        ArrayObject $expenseTransfersCollectionForRefund,
        PaymentUnzerTransfer $paymentUnzerTransfer
    ): ArrayObject {
        $mainMarketplaceUnzerCredentialsTransfer = $this->getMainMarketplaceUnzerCredentials($paymentUnzerTransfer);
        $childMarketplaceUnzerCredentialsCollectionTransfer = $this->getChildUnzerCredentialsCollection($mainMarketplaceUnzerCredentialsTransfer);

        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            $this->updateExpenseTransferWithParticipantId($childMarketplaceUnzerCredentialsCollectionTransfer, $expenseTransfer);
        }

        return $expenseTransfersCollectionForRefund;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    protected function getMainMarketplaceUnzerCredentials(PaymentUnzerTransfer $paymentUnzerTransfer): UnzerCredentialsTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())->addKeypairId($paymentUnzerTransfer->getKeypairIdOrFail()),
            );

        $unzerCredentialsTransfer = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsTransfer === null) {
            throw new UnzerException(sprintf('Unzer Credentials for keypair id %s not found.', $paymentUnzerTransfer->getKeypairIdOrFail()));
        }

        return $unzerCredentialsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $mainMarketplaceUnzerCredentialsTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer
     */
    protected function getChildUnzerCredentialsCollection(UnzerCredentialsTransfer $mainMarketplaceUnzerCredentialsTransfer): UnzerCredentialsCollectionTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())->addParentId($mainMarketplaceUnzerCredentialsTransfer->getIdUnzerCredentialsOrFail()),
            );

        return $this->unzerReader->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    protected function expandUnzerRefundTransfersWithExpenseTransfers(
        RefundTransfer $refundTransfer,
        ExpenseTransfer $expenseTransfer
    ): RefundTransfer {
        foreach ($refundTransfer->getUnzerRefunds() as $unzerRefundTransfer) {
            if ($unzerRefundTransfer->getChargeId() === $expenseTransfer->getUnzerChargeId()) {
                $unzerRefundTransfer->addItem($this->createUnzerRefundItemTransfer($expenseTransfer));
            }
        }

        return $refundTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerRefundItemTransfer
     */
    protected function createUnzerRefundItemTransfer(ExpenseTransfer $expenseTransfer): UnzerRefundItemTransfer
    {
        return (new UnzerRefundItemTransfer())->setBasketItemReferenceId(
            sprintf(UnzerConstants::UNZER_BASKET_SHIPMENT_REFERENCE_ID_TEMPLATE, $expenseTransfer->getUnzerParticipantIdOrFail()),
        )
            ->setQuantity(UnzerConstants::PARTIAL_REFUND_QUANTITY)
            ->setAmountGross($expenseTransfer->getRefundableAmountOrFail() / UnzerConstants::INT_TO_FLOAT_DIVIDER);
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfersCollectionForRefund
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \ArrayObject
     */
    protected function expandStandardExpensesWithChargeIds(
        ArrayObject $expenseTransfersCollectionForRefund,
        PaymentUnzerTransfer $paymentUnzerTransfer
    ): ArrayObject {
        if ($paymentUnzerTransfer->getIsAuthorizableOrFail()) {
            return $this->addChargeIdsForStandardExpenses($expenseTransfersCollectionForRefund);
        }

        $chargeId = $this->getChargeIdForDirectPayment($paymentUnzerTransfer);
        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            $expenseTransfer->setUnzerChargeId($chargeId);
        }

        return $expenseTransfersCollectionForRefund;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfersCollectionForRefund
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \ArrayObject
     */
    protected function expandMarketplaceExpensesWithChargeIds(
        ArrayObject $expenseTransfersCollectionForRefund,
        PaymentUnzerTransfer $paymentUnzerTransfer
    ): ArrayObject {
        if ($paymentUnzerTransfer->getIsAuthorizableOrFail()) {
            return $this->addChargeIdsForMarketplaceExpenses($expenseTransfersCollectionForRefund, $paymentUnzerTransfer);
        }

        $chargeId = $this->getChargeIdForDirectPayment($paymentUnzerTransfer);
        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            $expenseTransfer->setUnzerChargeId($chargeId);
        }

        return $expenseTransfersCollectionForRefund;
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfersCollectionForRefund
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    protected function processStandardExpensesRefund(
        RefundTransfer $refundTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer,
        ArrayObject $expenseTransfersCollectionForRefund
    ): RefundTransfer {
        $expenseTransfersCollectionForRefund = $this->expandStandardExpensesWithChargeIds($expenseTransfersCollectionForRefund, $paymentUnzerTransfer);

        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            $unzerRefundTransfer = $this->createStandardUnzerRefundTransfer($paymentUnzerTransfer, $expenseTransfer);
            $refundTransfer->addUnzerRefund($unzerRefundTransfer);
        }

        return $refundTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerRefundTransfer
     */
    protected function createStandardUnzerRefundTransfer(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        ExpenseTransfer $expenseTransfer
    ): UnzerRefundTransfer {
        return (new UnzerRefundTransfer())
            ->setIsMarketplace(false)
            ->setAmount($expenseTransfer->getRefundableAmountOrFail() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setPaymentId($paymentUnzerTransfer->getPaymentIdOrFail())
            ->setChargeId($expenseTransfer->getUnzerChargeIdOrFail());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer $childMarketplaceUnzerCredentialsCollectionTransfer
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return void
     */
    protected function updateExpenseTransferWithParticipantId(
        UnzerCredentialsCollectionTransfer $childMarketplaceUnzerCredentialsCollectionTransfer,
        ExpenseTransfer $expenseTransfer
    ): void {
        foreach ($childMarketplaceUnzerCredentialsCollectionTransfer->getUnzerCredentials() as $unzerCredentialsTransfer) {
            if (
                $expenseTransfer->getMerchantReference() === null
                && $unzerCredentialsTransfer->getTypeOrFail() === UnzerSharedConstants::UNZER_CREDENTIALS_TYPE_MARKETPLACE_MAIN_MERCHANT
            ) {
                $expenseTransfer->setUnzerParticipantId($unzerCredentialsTransfer->getParticipantIdOrFail());

                continue;
            }

            if ($expenseTransfer->getMerchantReference() === $unzerCredentialsTransfer->getMerchantReference()) {
                $expenseTransfer->setUnzerParticipantId($unzerCredentialsTransfer->getParticipantIdOrFail());
            }
        }
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \ArrayObject<\Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfersCollectionForRefund
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    protected function addExpenseRefunds(RefundTransfer $refundTransfer, ArrayObject $expenseTransfersCollectionForRefund): RefundTransfer
    {
        $expenseRefundAmount = 0;
        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            $expenseTransfer->setCanceledAmount($expenseTransfer->getRefundableAmount());
            $refundTransfer->addExpense($expenseTransfer);
            $expenseRefundAmount += $expenseTransfer->getRefundableAmountOrFail();
        }

        return $refundTransfer->setAmount($refundTransfer->getAmount() + $expenseRefundAmount);
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfersCollectionForRefund
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer>
     */
    protected function addChargeIdsForMarketplaceExpenses(
        ArrayObject $expenseTransfersCollectionForRefund,
        PaymentUnzerTransfer $paymentUnzerTransfer
    ): ArrayObject {
        $chargeIdsIndexedByParticipantId = $this->getChargeIdsIndexedByParticipantId($paymentUnzerTransfer);

        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            $expenseTransfer->setUnzerChargeId($chargeIdsIndexedByParticipantId[$expenseTransfer->getUnzerParticipantIdOrFail()]);
        }

        return $expenseTransfersCollectionForRefund;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return array<string, string>
     */
    protected function getChargeIdsIndexedByParticipantId(PaymentUnzerTransfer $paymentUnzerTransfer): array
    {
        $paymentUnzerOrderItemCollectionTransfer = $this->unzerRepository
            ->getPaymentUnzerOrderItemCollectionByOrderId($paymentUnzerTransfer->getOrderIdOrFail());

        $chargeIdsIndexedByParticipantId = [];
        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            $participantId = $paymentUnzerOrderItem->getParticipantIdOrFail();
            if (!isset($chargeIdsIndexedByParticipantId[$participantId]) && $paymentUnzerOrderItem->getChargeId() !== null) {
                $chargeIdsIndexedByParticipantId[$participantId] = $paymentUnzerOrderItem->getChargeIdOrFail();
            }
        }

        return $chargeIdsIndexedByParticipantId;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return string
     */
    protected function getChargeIdForDirectPayment(PaymentUnzerTransfer $paymentUnzerTransfer): string
    {
        $paymentUnzerOrderItemCollectionTransfer = $this->unzerRepository
            ->getPaymentUnzerOrderItemCollectionByOrderId($paymentUnzerTransfer->getOrderIdOrFail());

        return $paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems()->getIterator()->current()->getChargeIdOrFail();
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfersCollectionForRefund
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer>
     */
    protected function addChargeIdsForStandardExpenses(
        ArrayObject $expenseTransfersCollectionForRefund
    ): ArrayObject {
        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            /** @var \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer */
            $paymentUnzerShipmentChargeTransfer = $this->unzerRepository->findPaymentUnzerShipmentCharge($expenseTransfer->getShipmentOrFail()->getIdSalesShipmentOrFail());
            if ($paymentUnzerShipmentChargeTransfer === null) {
                continue;
            }

            $expenseTransfer->setUnzerChargeId($paymentUnzerShipmentChargeTransfer->getChargeIdOrFail());
        }

        return $expenseTransfersCollectionForRefund;
    }
}
