<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Refund;

use ArrayObject;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionConditionsTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCriteriaTransfer;
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
        $expenseTransfersCollectionForRefund = $this->expandExpensesWithChargeIds($expenseTransfersCollectionForRefund, $paymentUnzerTransfer);
        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            $refundTransfer->addUnzerRefund($this->createMarketplaceUnzerRefundTransfer($paymentUnzerTransfer, $expenseTransfer));
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
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerRefundTransfer
     */
    protected function createMarketplaceUnzerRefundTransfer(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        ExpenseTransfer $expenseTransfer
    ): UnzerRefundTransfer {
        return (new UnzerRefundTransfer())
            ->setIsMarketplace(true)
            ->setPaymentId($paymentUnzerTransfer->getPaymentIdOrFail())
            ->setChargeId($expenseTransfer->getUnzerChargeIdOrFail())
            ->addItem(
                (new UnzerRefundItemTransfer())
                    ->setBasketItemReferenceId(
                        sprintf(UnzerConstants::UNZER_BASKET_SHIPMENT_REFERENCE_ID_TEMPLATE, $expenseTransfer->getUnzerParticipantIdOrFail()),
                    )
                    ->setQuantity(UnzerConstants::PARTIAL_REFUND_QUANTITY)
                    ->setAmountGross($expenseTransfer->getRefundableAmountOrFail() / UnzerConstants::INT_TO_FLOAT_DIVIDER),
            );
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfersCollectionForRefund
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \ArrayObject
     */
    protected function expandExpensesWithChargeIds(ArrayObject $expenseTransfersCollectionForRefund, PaymentUnzerTransfer $paymentUnzerTransfer): ArrayObject
    {
        $paymentUnzerTransactionCollectionCollectionTransfer = $this->getPaymentUnzerTransactionCollectionTransfer($paymentUnzerTransfer);
        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            $expenseTransfer->setUnzerChargeId(
                $this->getChargeIdByParticipantId($paymentUnzerTransactionCollectionCollectionTransfer, (string)$expenseTransfer->getUnzerParticipantId()),
            );
        }

        return $expenseTransfersCollectionForRefund;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer
     */
    protected function getPaymentUnzerTransactionCollectionTransfer(PaymentUnzerTransfer $paymentUnzerTransfer): PaymentUnzerTransactionCollectionTransfer
    {
        $paymentUnzerTransactionCriteriaTransfer = (new PaymentUnzerTransactionCriteriaTransfer())
            ->setPaymentUnzerTransactionConditions(
                (new PaymentUnzerTransactionConditionsTransfer())
                    ->addPaymentUnzerId($paymentUnzerTransfer->getIdPaymentUnzerOrFail())
                    ->addType(UnzerConstants::TRANSACTION_TYPE_CHARGE),
            );

        return $this->unzerRepository
            ->getPaymentUnzerTransactionCollectionByCriteria($paymentUnzerTransactionCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer $paymentUnzerTransactionCollectionTransfer
     * @param string $participantId
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return string
     */
    protected function getChargeIdByParticipantId(
        PaymentUnzerTransactionCollectionTransfer $paymentUnzerTransactionCollectionTransfer,
        string $participantId
    ): string {
        foreach ($paymentUnzerTransactionCollectionTransfer->getPaymentUnzerTransactions() as $paymentUnzerTransactionTransfer) {
            if ($paymentUnzerTransactionTransfer->getParticipantId() === null) {
                return $paymentUnzerTransactionTransfer->getTransactionIdOrFail();
            }

            if ($paymentUnzerTransactionTransfer->getParticipantIdOrFail() === $participantId) {
                return $paymentUnzerTransactionTransfer->getTransactionIdOrFail();
            }
        }

        throw new UnzerException(sprintf('Unzer Charge Id not found for Participant ID %s', $participantId));
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
        $unzerRefundTransfer = $this->createStandardUnzerRefundTransfer($paymentUnzerTransfer, $expenseTransfersCollectionForRefund);

        return $refundTransfer->addUnzerRefund($unzerRefundTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfersCollectionForRefund
     *
     * @return \Generated\Shared\Transfer\UnzerRefundTransfer
     */
    protected function createStandardUnzerRefundTransfer(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        ArrayObject $expenseTransfersCollectionForRefund
    ): UnzerRefundTransfer {
        $refundAmountTotal = 0;
        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            $refundAmountTotal += (int)$expenseTransfer->getRefundableAmount();
        }

        return (new UnzerRefundTransfer())
            ->setIsMarketplace(false)
            ->setAmount($refundAmountTotal / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setPaymentId($paymentUnzerTransfer->getPaymentIdOrFail())
            ->setChargeId($this->getChargeIdByPaymentUnzer($paymentUnzerTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return string
     */
    protected function getChargeIdByPaymentUnzer(PaymentUnzerTransfer $paymentUnzerTransfer): string
    {
        $paymentUnzerTransactionCriteriaTransfer = (new PaymentUnzerTransactionCriteriaTransfer())
            ->setPaymentUnzerTransactionConditions(
                (new PaymentUnzerTransactionConditionsTransfer())
                    ->addPaymentUnzerId($paymentUnzerTransfer->getIdPaymentUnzerOrFail())
                    ->addType(UnzerConstants::TRANSACTION_TYPE_CHARGE),
            );

        $paymentUnzerTransactionCollection = $this->unzerRepository
            ->getPaymentUnzerTransactionCollectionByCriteria($paymentUnzerTransactionCriteriaTransfer);

        if ($paymentUnzerTransactionCollection->getPaymentUnzerTransactions()->count() === 0) {
            throw new UnzerException(sprintf('Unzer Charge Id not found for Unzer Payment ID %s', $paymentUnzerTransfer->getIdPaymentUnzer()));
        }

        /** @var \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer */
        $paymentUnzerTransactionTransfer = $paymentUnzerTransactionCollection->getPaymentUnzerTransactions()->getIterator()->current();

        return $paymentUnzerTransactionTransfer->getTransactionIdOrFail();
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer $childMarketplaceUnzerCredentialsCollectionTransfer
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return void
     */
    public function updateExpenseTransferWithParticipantId(
        UnzerCredentialsCollectionTransfer $childMarketplaceUnzerCredentialsCollectionTransfer,
        ExpenseTransfer $expenseTransfer
    ): void {
        foreach ($childMarketplaceUnzerCredentialsCollectionTransfer->getUnzerCredentials() as $unzerCredentialsTransfer) {
            if (
                $expenseTransfer->getMerchantReference() === null
                && $unzerCredentialsTransfer->getTypeOrFail() === UnzerSharedConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT
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
}
