<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Writer;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantConditionsTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
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
     * @var UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface $unzerEntityManager
     * @param UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(
        UnzerEntityManagerInterface $unzerEntityManager,
        UnzerReaderInterface $unzerReader,
        UnzerConfig $unzerConfig
    ) {
        $this->unzerEntityManager = $unzerEntityManager;
        $this->unzerReader = $unzerReader;
        $this->unzerConfig = $unzerConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantUnzerParticipantTransfer $merchantUnzerParticipantTransfer
     *
     * @return void
     */
    public function saveMerchantUnzerParticipant(MerchantUnzerParticipantTransfer $merchantUnzerParticipantTransfer): void
    {
         $this->unzerEntityManager->saveMerchantUnzerParticipantEntity($merchantUnzerParticipantTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function createUnzerPaymentDetails(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void
    {
        $paymentUnzerTransfer = $this->createPaymentUnzer($quoteTransfer, $saveOrderTransfer);

        foreach ($saveOrderTransfer->getOrderItems() as $orderItemTransfer) {
            $this->createPaymentUnzerOrderItemTransfer($paymentUnzerTransfer, $orderItemTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer $paymentUnzerTransactionCollectionTransfer
     *
     * @return void
     */
    public function updateUnzerPaymentDetails(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        PaymentUnzerOrderItemCollectionTransfer $paymentUnzerOrderItemCollectionTransfer,
        PaymentUnzerTransactionCollectionTransfer $paymentUnzerTransactionCollectionTransfer
    ): void {
        $this->unzerEntityManager->savePaymentUnzerEntity($paymentUnzerTransfer);

        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItemTransfer) {
            $this->unzerEntityManager->savePaymentUnzerOrderItemEntity($paymentUnzerOrderItemTransfer);
        }

        foreach ($paymentUnzerTransactionCollectionTransfer->getPaymentUnzerTransactions() as $paymentUnzerTransactionTransfer) {
            $this->unzerEntityManager->savePaymentUnzerTransactionEntity($paymentUnzerTransactionTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    protected function createPaymentUnzer(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): PaymentUnzerTransfer
    {
        $unzerPaymentTransfer = $quoteTransfer->getPaymentOrFail()->getUnzerPayment();

        $paymentUnzerTransfer = (new PaymentUnzerTransfer())
            ->setIdSalesOrder($saveOrderTransfer->getIdSalesOrder())
            ->setCustomerId($unzerPaymentTransfer->getCustomerOrFail()->getId())
            ->setOrderId($saveOrderTransfer->getOrderReference())
            ->setIsMarketplace($unzerPaymentTransfer->getIsMarketplace())
            ->setIsAuthorizable($unzerPaymentTransfer->getIsAuthorizable());

        return $this->unzerEntityManager->savePaymentUnzerEntity($paymentUnzerTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer
     */
    protected function createPaymentUnzerOrderItemTransfer(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        ItemTransfer $itemTransfer
    ): PaymentUnzerOrderItemTransfer {
        $paymentUnzerOrderItemTransfer = (new PaymentUnzerOrderItemTransfer())
            ->setIdPaymentUnzer($paymentUnzerTransfer->getIdPaymentUnzer())
            ->setIdSalesOrderItem($itemTransfer->getIdSalesOrderItem())
            ->setParticipantId($this->getParticipantIdForOrderItem($itemTransfer))
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
        $merchantUnzerParticipantCriteriaTransfer = (new MerchantUnzerParticipantCriteriaTransfer())
            ->setMerchantUnzerParticipantConditions(
                (new MerchantUnzerParticipantConditionsTransfer())->setReferences([$orderItem->getMerchantReference()]),
            );

        $merchantUnzerParticipantTransfer = $this->unzerReader
            ->getMerchantUnzerParticipantByCriteria($merchantUnzerParticipantCriteriaTransfer);

        if ($merchantUnzerParticipantTransfer !== null) {
            return $merchantUnzerParticipantTransfer->getParticipantId();
        }

        return null;
    }
}
