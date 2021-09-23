<?php

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
        $unzerBasketTransfer->setAmountTotalGross($quoteTransfer->getTotals()->getGrandTotal() / 100);
        $unzerBasketTransfer->setAmountTotalVat($quoteTransfer->getTotals()->getTaxTotal()->getAmount() / 100);
        $unzerBasketTransfer->setCurrencyCode($quoteTransfer->getCurrency()->getCode());
        $unzerBasketTransfer->setNote('');
        $unzerBasketTransfer->setOrderId($quoteTransfer->getPayment()->getUnzerPayment()->getOrderId());
        $unzerBasketTransfer->setBasketItems($this->mapQuoteItemsCollectionToBasketItemsCollection($quoteTransfer));

        return $unzerBasketTransfer;
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
        $unzerPaymentResourceTransfer->setType(
            $this->mapUnzerPaymentType($quoteTransfer->getPayment()->getPaymentSelection())
        );

        return $unzerPaymentResourceTransfer;
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
        $unzerBasketItemTransfer->setBasketItemReferenceId($itemTransfer->getSku());
        $unzerBasketItemTransfer->setQuantity($itemTransfer->getQuantity());
        $unzerBasketItemTransfer->setAmountGross($itemTransfer->getSumGrossPrice() / 100);
        $unzerBasketItemTransfer->setAmountVat($itemTransfer->getSumTaxAmount() / 100);
        $unzerBasketItemTransfer->setAmountDiscount($itemTransfer->getSumDiscountAmountAggregation() / 100);
        $unzerBasketItemTransfer->setAmountPerUnit($itemTransfer->getUnitPriceToPayAggregation() / 100);
        $unzerBasketItemTransfer->setAmountNet($itemTransfer->getSumNetPrice() / 100);
        $unzerBasketItemTransfer->setTitle($itemTransfer->getName());
        $unzerBasketItemTransfer->setParticipantId($itemTransfer->getUnzerParticipantId());
        $unzerBasketItemTransfer->setType('Wire');

        return $unzerBasketItemTransfer;
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
