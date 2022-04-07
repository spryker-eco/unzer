<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Checkout\ExpensesDistributor;

use ArrayObject;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerBasketItemTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerExpensesDistributor implements UnzerExpensesDistributorInterface
{
    /**
     * @var int
     */
    protected const DEFAULT_QUANTITY = 1;

    /**
     * @var int
     */
    protected const DEFAULT_ZERO_AMOUNT = 0;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    public function distributeExpensesBetweenQuoteItems(QuoteTransfer $quoteTransfer, UnzerBasketTransfer $unzerBasketTransfer): UnzerBasketTransfer
    {
        $expensesCollection = $quoteTransfer->getExpenses();

        if ($expensesCollection->count() === 0) {
            return $unzerBasketTransfer;
        }

        if ($quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getIsMarketplace()) {
            return $this->addGroupedExpensesByParticipantId($unzerBasketTransfer, $expensesCollection);
        }

        return $this->addStandardExpenses($unzerBasketTransfer, $expensesCollection);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenses
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    protected function addGroupedExpensesByParticipantId(UnzerBasketTransfer $unzerBasketTransfer, ArrayObject $expenses): UnzerBasketTransfer
    {
        $expensesGroupedByParticipantId = [];
        foreach ($expenses as $expenseTransfer) {
            $expensesGroupedByParticipantId[(string)$expenseTransfer->getUnzerParticipantId()][] = $expenseTransfer;
        }

        foreach ($expensesGroupedByParticipantId as $participantId => $expensesCollection) {
            $referenceId = sprintf(UnzerConstants::UNZER_MARKETPLACE_BASKET_SHIPMENT_REFERENCE_ID, $participantId);

            $unzerBasketItemTransfer = $this
                ->createUnzerBasketItemTransfer()
                ->setParticipantId($participantId)
                ->setBasketItemReferenceId($referenceId);

            /** @phpstan-var array<string, \Generated\Shared\Transfer\ExpenseTransfer> $expensesCollection */
            foreach ($expensesCollection as $expenseTransfer) {
                $unzerBasketItemTransfer->setAmountPerUnit(
                    $unzerBasketItemTransfer->getAmountPerUnit() +
                    $expenseTransfer->getSumGrossPrice() / UnzerConstants::INT_TO_FLOAT_DIVIDER,
                );
                $unzerBasketItemTransfer->setVat((int)$expenseTransfer->getTaxRate());
            }

            $unzerBasketTransfer->addBasketItem($unzerBasketItemTransfer);
        }

        return $unzerBasketTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ExpenseTransfer> $expenses
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    protected function addStandardExpenses(UnzerBasketTransfer $unzerBasketTransfer, ArrayObject $expenses): UnzerBasketTransfer
    {
        $unzerBasketItemTransfer = $this->createUnzerBasketItemTransfer()
            ->setBasketItemReferenceId(UnzerConstants::UNZER_BASKET_SHIPMENT_REFERENCE_ID);
        foreach ($expenses as $expenseTransfer) {
            $unzerBasketItemTransfer->setAmountPerUnit(
                $unzerBasketItemTransfer->getAmountPerUnit() +
                $expenseTransfer->getSumGrossPrice() / UnzerConstants::INT_TO_FLOAT_DIVIDER,
            );
            $unzerBasketItemTransfer->setVat((int)$expenseTransfer->getTaxRate());
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
            ->setAmountPerUnit(static::DEFAULT_ZERO_AMOUNT)
            ->setVat(static::DEFAULT_ZERO_AMOUNT);
    }
}
