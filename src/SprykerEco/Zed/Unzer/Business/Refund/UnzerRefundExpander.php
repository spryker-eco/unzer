<?php

namespace SprykerEco\Zed\Unzer\Business\Refund;

use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionConditionsTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCriteriaTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
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
     * @var UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @param UnzerReaderInterface $unzerReader
     * @param UnzerRepositoryInterface $unzerRepository
     */
    public function __construct(
        UnzerReaderInterface $unzerReader,
        UnzerRepositoryInterface $unzerRepository
    )
    {
        $this->unzerReader = $unzerReader;
        $this->unzerRepository = $unzerRepository;
    }

    /**
     * @param RefundTransfer $refundTransfer
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \ArrayObject|array<ExpenseTransfer> $expenseTransfersCollectionForRefund
     *
     * @return RefundTransfer
     */
    public function expandRefundWithUnzerRefundCollection(
        RefundTransfer $refundTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer,
        \ArrayObject $expenseTransfersCollectionForRefund
    ): RefundTransfer
    {
        if ($paymentUnzerTransfer->getIsMarketplace()) {
            return $this->processMarketplaceExpensesRefund($refundTransfer, $paymentUnzerTransfer, $expenseTransfersCollectionForRefund);
        }

        return $this->processStandardExpensesRefund($refundTransfer, $paymentUnzerTransfer, $expenseTransfersCollectionForRefund);
    }

    /**
     * @param RefundTransfer $refundTransfer
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \ArrayObject|array<ExpenseTransfer> $expenseTransfersCollectionForRefund
     *
     * @return RefundTransfer
     *
     * @throws UnzerException
     */
    protected function processMarketplaceExpensesRefund(
        RefundTransfer       $refundTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer,
        \ArrayObject          $expenseTransfersCollectionForRefund
    ): RefundTransfer
    {
        $expenseTransfersCollectionForRefund = $this->expandExpensesWithParticipantIds($expenseTransfersCollectionForRefund, $paymentUnzerTransfer);
        $expenseTransfersCollectionForRefund = $this->expandExpensesWithChargeIds($expenseTransfersCollectionForRefund, $paymentUnzerTransfer);
        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            $refundTransfer->addUnzerRefund($this->createMarketplaceUnzerRefund($paymentUnzerTransfer, $expenseTransfer));
        }

        return $refundTransfer;
    }

    /**
     * @param \ArrayObject|array<ExpenseTransfer> $expenseTransfersCollectionForRefund
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \ArrayObject
     *
     * @throws UnzerException
     */
    protected function expandExpensesWithParticipantIds(\ArrayObject $expenseTransfersCollectionForRefund, PaymentUnzerTransfer $paymentUnzerTransfer): \ArrayObject
    {
        $mainMarketplaceUnzerCredentialsTransfer = $this->getMainMarketplaceUnzerCredentials($paymentUnzerTransfer);
        $childMarketplaceUnzerCredentialsCollectionTransfer = $this->getChildUnzerCredentialsCollection($mainMarketplaceUnzerCredentialsTransfer);

        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            /** @var ExpenseTransfer $expenseTransfer */
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

        return $expenseTransfersCollectionForRefund;
    }

    /**
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return UnzerCredentialsTransfer
     *
     * @throws UnzerException
     */
    protected function getMainMarketplaceUnzerCredentials(PaymentUnzerTransfer $paymentUnzerTransfer): UnzerCredentialsTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())->addKeypairId($paymentUnzerTransfer->getKeypairIdOrFail())
            );

        $unzerCredentialsTransfer = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsTransfer === null) {
            throw new UnzerException(sprintf('Unzer Credentials for keypair id %s not found.', $paymentUnzerTransfer->getKeypairIdOrFail()));
        }

        return $unzerCredentialsTransfer;
    }

    /**
     * @param UnzerCredentialsTransfer $mainMarketplaceUnzerCredentialsTransfer
     *
     * @return UnzerCredentialsCollectionTransfer
     */
    protected function getChildUnzerCredentialsCollection(UnzerCredentialsTransfer $mainMarketplaceUnzerCredentialsTransfer): UnzerCredentialsCollectionTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())->addParentId($mainMarketplaceUnzerCredentialsTransfer->getIdUnzerCredentialsOrFail())
            );

        return $this->unzerReader->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);
    }

    /**
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     * @param ExpenseTransfer $expenseTransfer
     *
     * @return UnzerRefundTransfer
     */
    protected function createMarketplaceUnzerRefund(PaymentUnzerTransfer $paymentUnzerTransfer, ExpenseTransfer $expenseTransfer): UnzerRefundTransfer
    {
        $unzerRefundTransfer = (new UnzerRefundTransfer())
            ->setPaymentId($paymentUnzerTransfer->getPaymentIdOrFail())
            ->setChargeId($expenseTransfer->getUnzerChargeIdOrFail())
            ->addItem(
                (new UnzerRefundItemTransfer())
                    ->setBasketItemReferenceId(
                        sprintf(UnzerConstants::UNZER_MARKETPLACE_BASKET_SHIPMENT_REFERENCE_ID, $expenseTransfer->getUnzerParticipantIdOrFail())
                    )
                    ->setQuantity(UnzerConstants::PARTIAL_REFUND_QUANTITY)
                    ->setAmountGross($expenseTransfer->getRefundableAmountOrFail() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            );

        return $unzerRefundTransfer;
    }

    /**
     * @param \ArrayObject|array<ExpenseTransfer> $expenseTransfersCollectionForRefund
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     * @return \ArrayObject
     * @throws UnzerException
     */
    protected function expandExpensesWithChargeIds(\ArrayObject $expenseTransfersCollectionForRefund, PaymentUnzerTransfer $paymentUnzerTransfer): \ArrayObject
    {
        $paymentUnzerTransactionCollectionCollectionTransfer = $this->getPaymentUnzerTransactionCollectionTransfer($paymentUnzerTransfer);
        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            $expenseTransfer->setUnzerChargeId(
                $this->getChargeIdByParticipantId($paymentUnzerTransactionCollectionCollectionTransfer, $expenseTransfer->getUnzerParticipantId())
            );
        }

        return $expenseTransfersCollectionForRefund;
    }

    /**
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return PaymentUnzerTransactionCollectionTransfer
     */
    protected function getPaymentUnzerTransactionCollectionTransfer(PaymentUnzerTransfer $paymentUnzerTransfer): PaymentUnzerTransactionCollectionTransfer
    {
//        if (!$paymentUnzerTransfer->getIsAuthorizable()) {
//            //Sofort and BankTransfer transactions do not have participantId
//            $participantIds = null;
//        }

        $paymentUnzerTransactionCriteriaTransfer = (new PaymentUnzerTransactionCriteriaTransfer())
            ->setPaymentUnzerTransactionConditions(
                (new PaymentUnzerTransactionConditionsTransfer())
                    ->addFkPaymentUnzerId($paymentUnzerTransfer->getIdPaymentUnzer())
                    ->addType(UnzerConstants::TRANSACTION_TYPE_CHARGE)
            );

        return $this->unzerRepository
            ->findPaymentUnzerTransactionCollectionByCriteria($paymentUnzerTransactionCriteriaTransfer);
    }

    /**
     * @param PaymentUnzerTransactionCollectionTransfer $paymentUnzerTransactionCollectionTransfer
     * @param string $participantId
     *
     * @return string
     * @throws UnzerException
     */
    protected function getChargeIdByParticipantId(
        PaymentUnzerTransactionCollectionTransfer $paymentUnzerTransactionCollectionTransfer,
        string $participantId
    ): string
    {
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
     * @param RefundTransfer $refundTransfer
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \ArrayObject|array<ExpenseTransfer> $expenseTransfersCollectionForRefund
     *
     * @return RefundTransfer
     */
    protected function processStandardExpensesRefund(
        RefundTransfer $refundTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer,
        \ArrayObject $expenseTransfersCollectionForRefund
    ): RefundTransfer
    {
        $unzerRefundTransfer = $this->createStandardUnzerRefund($paymentUnzerTransfer, $expenseTransfersCollectionForRefund);

        return $refundTransfer->addUnzerRefund($unzerRefundTransfer);
    }

    /**
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \ArrayObject|array<ExpenseTransfer> $expenseTransfersCollectionForRefund
     *
     * @return UnzerRefundTransfer
     *
     * @throws UnzerException
     */
    protected function createStandardUnzerRefund(PaymentUnzerTransfer $paymentUnzerTransfer, \ArrayObject $expenseTransfersCollectionForRefund): UnzerRefundTransfer
    {
        $refundAmountTotal = 0;
        foreach ($expenseTransfersCollectionForRefund as $expenseTransfer) {
            /** @var ExpenseTransfer $expenseTransfer */
            $refundAmountTotal += (int)$expenseTransfer->getRefundableAmount();
        }

        return (new UnzerRefundTransfer())
            ->setAmount($refundAmountTotal / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setPaymentId($paymentUnzerTransfer->getPaymentIdOrFail())
            ->setChargeId($this->getChargeIdByPaymentUnzer($paymentUnzerTransfer));
    }

    /**
     * @param PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return string
     *
     * @throws UnzerException
     */
    protected function getChargeIdByPaymentUnzer(PaymentUnzerTransfer $paymentUnzerTransfer): string
    {
        $paymentUnzerTransactionCriteriaTransfer = (new PaymentUnzerTransactionCriteriaTransfer())
            ->setPaymentUnzerTransactionConditions(
                (new PaymentUnzerTransactionConditionsTransfer())
                    ->addFkPaymentUnzerId($paymentUnzerTransfer->getIdPaymentUnzer())
                    ->addType(UnzerConstants::TRANSACTION_TYPE_CHARGE)
            );

        $paymentUnzerTransactionCollection = $this->unzerRepository
            ->findPaymentUnzerTransactionCollectionByCriteria($paymentUnzerTransactionCriteriaTransfer);

        if ($paymentUnzerTransactionCollection->getPaymentUnzerTransactions()->count() === 0) {
            throw new UnzerException(sprintf('Unzer Charge Id not found for Unzer Payment ID %s', $paymentUnzerTransfer->getIdPaymentUnzer()));
        }

        /** @var PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer */
        $paymentUnzerTransactionTransfer = $paymentUnzerTransactionCollection->getPaymentUnzerTransactions()->getIterator()->current();

        return $paymentUnzerTransactionTransfer->getTransactionIdOrFail();
    }
}
