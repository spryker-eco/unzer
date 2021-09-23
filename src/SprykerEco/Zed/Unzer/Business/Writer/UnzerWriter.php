<?php

namespace SprykerEco\Zed\Unzer\Business\Writer;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerWriter implements UnzerWriterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface
     */
    protected $unzerEntityManager;

    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface $unzerEntityManager
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface $unzerRepository
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(
        UnzerEntityManagerInterface $unzerEntityManager,
        UnzerRepositoryInterface $unzerRepository,
        UnzerConfig $unzerConfig
    ) {
        $this->unzerEntityManager = $unzerEntityManager;
        $this->unzerRepository = $unzerRepository;
        $this->unzerConfig = $unzerConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer $merchantUnzerParticipantTransfer
     *
     * @return void
     */
    public function saveMerchantUnzerParticipantEntity(MerchantUnzerParticipantTransfer $merchantUnzerParticipantTransfer): void
    {
         $this->unzerEntityManager->saveMerchantUnzerParticipantEntity($merchantUnzerParticipantTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveUnzerPaymentEntities(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void
    {
        $paymentUnzerTransfer = $this->createPaymentUnzerEntity($quoteTransfer, $saveOrderTransfer);

        foreach ($saveOrderTransfer->getOrderItems() as $orderItem) {
            $this->createPaymentUnzerOrderItemEntity($paymentUnzerTransfer, $orderItem);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer $paymentUnzerTransactionCollectionTransfer
     *
     * @return void
     */
    public function updateUnzerPaymentEntities(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
        PaymentUnzerTransactionCollectionTransfer $paymentUnzerTransactionCollectionTransfer
    ): void {
        $this->unzerEntityManager->savePaymentUnzerEntity($paymentUnzerTransfer);

        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            $this->unzerEntityManager->savePaymentUnzerOrderItemEntity($paymentUnzerOrderItem);
        }

        foreach ($paymentUnzerTransactionCollectionTransfer->getPaymentUnzerTransactions() as $paymentUnzerTransaction) {
            $this->unzerEntityManager->savePaymentUnzerTransactionEntity($paymentUnzerTransaction);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    protected function createPaymentUnzerEntity(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): PaymentUnzerTransfer
    {
        $unzerPaymentTransfer = $quoteTransfer->getPayment()->getUnzerPayment();

        $paymentUnzerTransfer = (new PaymentUnzerTransfer())
            ->setIdSalesOrder($saveOrderTransfer->getIdSalesOrder())
            ->setCustomerId($unzerPaymentTransfer->getCustomer()->getId())
            ->setOrderId($saveOrderTransfer->getOrderReference())
            ->setIsMarketplace($unzerPaymentTransfer->getIsMarketplace())
            ->setIsAuthorizable($unzerPaymentTransfer->getIsAuthorizable());

        return $this->unzerEntityManager->savePaymentUnzerEntity($paymentUnzerTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItem
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer
     */
    protected function createPaymentUnzerOrderItemEntity(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        ItemTransfer $orderItem
    ): PaymentUnzerOrderItemTransfer {
        $paymentUnzerOrderItemTransfer = (new PaymentUnzerOrderItemTransfer())
            ->setIdPaymentUnzer($paymentUnzerTransfer->getIdPaymentUnzer())
            ->setIdSalesOrderItem($orderItem->getIdSalesOrderItem())
            ->setParticipantId($this->getParticipantIdForOrderItem($orderItem))
            ->setStatus($this->unzerConfig->getOmsStatusNew());

        return $this->unzerEntityManager->savePaymentUnzerOrderItemEntity($paymentUnzerOrderItemTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItem
     *
     * @return string|null
     */
    protected function getParticipantIdForOrderItem(ItemTransfer $orderItem): ?string
    {
        $merchantUnzerParticipantTransfer = $this->unzerRepository
            ->findMerchantUnzerParticipantByMerchantReference($orderItem->getMerchantReference());
        if ($merchantUnzerParticipantTransfer !== null) {
            return $merchantUnzerParticipantTransfer->getParticipantId();
        }

        return null;
    }
}
