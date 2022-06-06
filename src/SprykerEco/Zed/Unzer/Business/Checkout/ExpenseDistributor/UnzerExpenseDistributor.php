<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Checkout\ExpenseDistributor;

use ArrayObject;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerBasketItemTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerExpenseDistributor implements UnzerExpenseDistributorInterface
{
    /**
     * @var int
     */
    protected const DEFAULT_QUANTITY = 1;

    /**
     * @var int
     */
    protected const DEFAULT_AMOUNT = 0;

    /**
     * @var int
     */
    protected const DEFAULT_VAT_VALUE = 0;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    public function distributeExpensesBetweenQuoteItems(QuoteTransfer $quoteTransfer, UnzerBasketTransfer $unzerBasketTransfer): UnzerBasketTransfer
    {
        $expenseTransfers = $quoteTransfer->getExpenses();

        if ($expenseTransfers->count() === 0) {
            return $unzerBasketTransfer;
        }

        if ($quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getIsMarketplace()) {
            $expenseTransfersGroupedByParticipantId = $this->getExpenseTransfersGroupedByParticipantId($expenseTransfers);

            return $this->addExpensesGroupedByParticipantIdToUnzerBasket($unzerBasketTransfer, $expenseTransfersGroupedByParticipantId);
        }

        return $this->addStandardExpenses($unzerBasketTransfer, $expenseTransfers);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     * @param array<string, array<int, \Generated\Shared\Transfer\ExpenseTransfer>> $expensesGroupedByParticipantId
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    protected function addExpensesGroupedByParticipantIdToUnzerBasket(
        UnzerBasketTransfer $unzerBasketTransfer,
        array $expensesGroupedByParticipantId
    ): UnzerBasketTransfer {
        foreach ($expensesGroupedByParticipantId as $participantId => $expenseTransfers) {
            $referenceId = sprintf(UnzerConstants::UNZER_BASKET_SHIPMENT_REFERENCE_ID_TEMPLATE, $participantId);

            $unzerBasketItemTransfer = $this
                ->createUnzerBasketItemTransfer()
                ->setParticipantId($participantId)
                ->setBasketItemReferenceId($referenceId);

            $unzerBasketItemTransfer = $this->addExpensesToUnzerBasketItem($expenseTransfers, $unzerBasketItemTransfer);
            if ($unzerBasketItemTransfer->getAmountPerUnit() === 0.0) {
                continue;
            }

            $unzerBasketTransfer->addBasketItem($unzerBasketItemTransfer);
        }

        return $unzerBasketTransfer;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfers
     *
     * @return array<string, array<int, \Generated\Shared\Transfer\ExpenseTransfer>>
     */
    protected function getExpenseTransfersGroupedByParticipantId(ArrayObject $expenseTransfers): array
    {
        $expenseTransfersGroupedByParticipantId = [];
        foreach ($expenseTransfers as $expenseTransfer) {
            $expenseTransfersGroupedByParticipantId[$expenseTransfer->getUnzerParticipantIdOrFail()][] = $expenseTransfer;
        }

        return $expenseTransfersGroupedByParticipantId;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfers
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    protected function addStandardExpenses(UnzerBasketTransfer $unzerBasketTransfer, ArrayObject $expenseTransfers): UnzerBasketTransfer
    {
        $unzerBasketItemTransfer = $this->createUnzerBasketItemTransfer()
            ->setBasketItemReferenceId(UnzerConstants::UNZER_BASKET_SHIPMENT_REFERENCE_ID);
        $unzerBasketItemTransfer = $this->addExpensesToUnzerBasketItem($expenseTransfers->getArrayCopy(), $unzerBasketItemTransfer);
        if ($unzerBasketItemTransfer->getAmountPerUnit() === 0.0) {
            return $unzerBasketTransfer;
        }

        return $unzerBasketTransfer->addBasketItem($unzerBasketItemTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerBasketItemTransfer
     */
    protected function createUnzerBasketItemTransfer(): UnzerBasketItemTransfer
    {
        return (new UnzerBasketItemTransfer())
            ->setTitle(UnzerConstants::UNZER_BASKET_SHIPMENT_TITLE)
            ->setType(UnzerConstants::UNZER_BASKET_TYPE_SHIPMENTS)
            ->setQuantity(static::DEFAULT_QUANTITY)
            ->setAmountPerUnit(static::DEFAULT_AMOUNT)
            ->setVat((string)static::DEFAULT_AMOUNT);
    }

    /**
     * @param array<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenseTransfers
     * @param \Generated\Shared\Transfer\UnzerBasketItemTransfer $unzerBasketItemTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketItemTransfer
     */
    protected function addExpensesToUnzerBasketItem(array $expenseTransfers, UnzerBasketItemTransfer $unzerBasketItemTransfer): UnzerBasketItemTransfer
    {
        foreach ($expenseTransfers as $expenseTransfer) {
            $unzerBasketItemTransfer->setAmountPerUnit(
                $unzerBasketItemTransfer->getAmountPerUnit() +
                $expenseTransfer->getSumPriceToPayAggregationOrFail() / UnzerConstants::INT_TO_FLOAT_DIVIDER,
            );
            $unzerBasketItemTransfer->setVat((string)$this->getVatValue($expenseTransfer));
        }

        return $unzerBasketItemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return int
     */
    protected function getVatValue(ExpenseTransfer $expenseTransfer): int
    {
        if ($expenseTransfer->getTaxRate() !== null) {
            return (int)$expenseTransfer->getTaxRate();
        }

        return static::DEFAULT_VAT_VALUE;
    }
}
