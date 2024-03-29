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
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerCheckoutMapper implements UnzerCheckoutMapperInterface
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
        return $unzerBasketTransfer
            ->setAmountTotalGross((float)$quoteTransfer->getTotalsOrFail()->getGrandTotal() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setAmountTotalVat((float)$quoteTransfer->getTotalsOrFail()->getTaxTotalOrFail()->getAmount() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setCurrencyCode($quoteTransfer->getCurrencyOrFail()->getCode())
            ->setNote('')
            ->setOrderId($quoteTransfer->getPaymentOrFail()->getUnzerPaymentOrFail()->getOrderId())
            ->setBasketItems($this->mapQuoteTransferToUnzerBasketItemTransferCollection($quoteTransfer));
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
            $this->unzerConfig
                ->getUnzerPaymentMethodKey(
                    $quoteTransfer->getPaymentOrFail()->getPaymentSelectionOrFail(),
                ),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\UnzerBasketItemTransfer>
     */
    protected function mapQuoteTransferToUnzerBasketItemTransferCollection(QuoteTransfer $quoteTransfer): ArrayObject
    {
        $unzerBasketItemTransferCollection = new ArrayObject();
        foreach ($quoteTransfer->getItems() as $quoteItemTransfer) {
            $unzerBasketItemTransferCollection->append($this->mapQuoteItemTransferToUnzerBasketItemTransfer($quoteItemTransfer, new UnzerBasketItemTransfer()));
        }

        return $unzerBasketItemTransferCollection;
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
        return $unzerBasketItemTransfer
            ->setBasketItemReferenceId($this->createBasketItemReferenceId($itemTransfer))
            ->setQuantity($itemTransfer->getQuantity())
            ->setVat((string)$itemTransfer->getTaxRate())
            ->setAmountPerUnit(
                $itemTransfer->getUnitPriceToPayAggregationOrFail() / UnzerConstants::INT_TO_FLOAT_DIVIDER,
            )
            ->setTitle($itemTransfer->getName())
            ->setParticipantId($itemTransfer->getUnzerParticipantId())
            ->setType(UnzerConstants::UNZER_BASKET_TYPE_GOODS);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return string
     */
    protected function createBasketItemReferenceId(ItemTransfer $itemTransfer): string
    {
        return sprintf(
            UnzerConstants::UNZER_BASKET_ITEM_REFERENCE_ID_TEMPLATE,
            $itemTransfer->getGroupKeyOrFail(),
            $itemTransfer->getIdSalesOrderItemOrFail(),
        );
    }
}
