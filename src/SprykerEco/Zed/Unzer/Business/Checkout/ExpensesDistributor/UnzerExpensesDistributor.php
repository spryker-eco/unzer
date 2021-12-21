<?php

namespace SprykerEco\Zed\Unzer\Business\Checkout\ExpensesDistributor;

use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;

class UnzerExpensesDistributor implements UnzerExpensesDistributorInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return QuoteTransfer
     */
    public function distributeExpensesBetweenQuoteItems(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $expensesCollection = $quoteTransfer->getExpenses();

        if ($expensesCollection->count() === 0) {
            return $quoteTransfer;
        }

        $itemCountersPerExpense = [];

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $shipmentTransfer = $itemTransfer->getShipment();
            if ($shipmentTransfer === null) {
                continue;
            }

            foreach ($expensesCollection as $expenseId => $expenseTransfer) {
                if ($this->assertSameShipment($shipmentTransfer, $expenseTransfer)) {
                    $itemTransfer->setExpenseId($expenseId);
                    $this->raiseExpenseIdCounter($itemCountersPerExpense, $expenseId);
                }
            }
        }

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            if ($itemTransfer->getExpenseId() === null) {
                continue;
            }

            $expenseTransfer = $expensesCollection[$itemTransfer->getExpenseId()] ?? null;
            if ($expenseTransfer === null) {
                continue;
            }

            $calculatedExpenseCost = intdiv($expenseTransfer->getSumGrossPrice(), $itemCountersPerExpense[$itemTransfer->getExpenseId()]);
            $itemTransfer->setCalculatedExpensesCost($calculatedExpenseCost);
        }


        return $quoteTransfer;
    }

    /**
     * @param ShipmentTransfer $shipmentTransfer
     * @param ExpenseTransfer $expenseTransfer
     *
     * @return bool
     */
    protected function assertSameShipment(ShipmentTransfer $shipmentTransfer, ExpenseTransfer $expenseTransfer): bool
    {
        return $shipmentTransfer->getShipmentSelection() === $expenseTransfer->getShipment()->getShipmentSelection() &&
            $shipmentTransfer->getMerchantReference() === $expenseTransfer->getShipment()->getMerchantReference();
    }

    /**
     * @param array $itemCountersPerExpense
     * @param int $expenseId
     *
     * @return void
     */
    protected function raiseExpenseIdCounter(array &$itemCountersPerExpense, int $expenseId): void
    {
        isset($itemCountersPerExpense[$expenseId]) ? $itemCountersPerExpense[$expenseId] += 1 : $itemCountersPerExpense[$expenseId] = 1;
    }
}
