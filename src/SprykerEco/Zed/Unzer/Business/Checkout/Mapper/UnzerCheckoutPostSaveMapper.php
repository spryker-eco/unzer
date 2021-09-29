<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Checkout\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerBasketItemTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerCheckoutPostSaveMapper implements UnzerCheckoutMapperInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(UnzerConfig $unzerConfig)
    {
        $this->unzerConfig = $unzerConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    public function mapQuoteTransferToUnzerBasketTransfer(
        QuoteTransfer $quoteTransfer,
        UnzerBasketTransfer $unzerBasketTransfer
    ): UnzerBasketTransfer {
        return $unzerBasketTransfer->setAmountTotalGross($quoteTransfer->getTotals()->getGrandTotal() / 100)
            ->setAmountTotalVat($quoteTransfer->getTotals()->getTaxTotal()->getAmount() / 100)
            ->setCurrencyCode($quoteTransfer->getCurrency()->getCode())
            ->setNote('')
            ->setOrderId($quoteTransfer->getPayment()->getUnzerPayment()->getOrderId())
            ->setBasketItems($this->mapQuoteItemsCollectionToBasketItemsCollection($quoteTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentResourceTransfer
     */
    public function mapQuoteTransferToUnzerPaymentResourceTransfer(
        QuoteTransfer $quoteTransfer,
        UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer
    ): UnzerPaymentResourceTransfer {
        return $unzerPaymentResourceTransfer->setType(
            $this->mapUnzerPaymentType($quoteTransfer->getPayment()->getPaymentSelection())
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\UnzerBasketItemTransfer[]
     */
    protected function mapQuoteItemsCollectionToBasketItemsCollection(QuoteTransfer $quoteTransfer): ArrayObject
    {
        $basketItems = new ArrayObject();
        foreach ($quoteTransfer->getItems() as $quoteItemTransfer) {
            $basketItems->append(
                $this->mapQuoteItemTransferToUnzerBasketItemTransfer($quoteItemTransfer, new UnzerBasketItemTransfer())
            );
        }

        return $basketItems;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\UnzerBasketItemTransfer $unzerBasketItemTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketItemTransfer
     */
    protected function mapQuoteItemTransferToUnzerBasketItemTransfer(
        ItemTransfer $itemTransfer,
        UnzerBasketItemTransfer $unzerBasketItemTransfer
    ): UnzerBasketItemTransfer {
        return $unzerBasketItemTransfer->setBasketItemReferenceId($itemTransfer->getSku())
            ->setQuantity($itemTransfer->getQuantity())
            ->setAmountGross($itemTransfer->getSumGrossPrice() / 100)
            ->setAmountVat($itemTransfer->getSumTaxAmount() / 100)
            ->setAmountDiscount($itemTransfer->getSumDiscountAmountAggregation() / 100)
            ->setAmountPerUnit($itemTransfer->getUnitPriceToPayAggregation() / 100)
            ->setAmountNet($itemTransfer->getSumNetPrice() / 100)
            ->setTitle($itemTransfer->getName())
            ->setParticipantId($itemTransfer->getUnzerParticipantId())
            ->setType('Wire');
    }

    /**
     * @param string $paymentSelection
     *
     * @return string
     */
    protected function mapUnzerPaymentType(string $paymentSelection): string
    {
        return $this->unzerConfig->getUnzerPaymentMethodKey($paymentSelection);
    }
}
