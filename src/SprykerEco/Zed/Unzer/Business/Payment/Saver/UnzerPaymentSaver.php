<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Saver;

use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as UnzerSharedConfig;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface;

class UnzerPaymentSaver implements UnzerPaymentSaverInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface
     */
    protected $unzerWriter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface $unzerWriter
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface $unzerPaymentMapper
     */
    public function __construct(
        UnzerReaderInterface $unzerReader,
        UnzerWriterInterface $unzerWriter,
        UnzerPaymentMapperInterface $unzerPaymentMapper
    ) {
        $this->unzerReader = $unzerReader;
        $this->unzerWriter = $unzerWriter;
        $this->unzerPaymentMapper = $unzerPaymentMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void
    {
        if ($quoteTransfer->getPaymentOrFail()->getPaymentProvider() !== UnzerSharedConfig::PAYMENT_PROVIDER_TYPE) {
            return;
        }

        $paymentUnzerTransfer = $this->unzerReader->getPaymentUnzerByOrderReference($saveOrderTransfer->getOrderReferenceOrFail());
        if ($paymentUnzerTransfer->getIdPaymentUnzer() !== null) {
            throw new UnzerException(sprintf('Order with reference %s already exists!', $saveOrderTransfer->getOrderReferenceOrFail()));
        }

        $this->unzerWriter->createUnzerPaymentDetails($quoteTransfer, $saveOrderTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param string $orderItemStatus
     * @param array|null $filteredSalesOrderItemIds
     *
     * @return void
     */
    public function saveUnzerPaymentDetails(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        string $orderItemStatus,
        array $filteredSalesOrderItemIds = []
    ): void {
        $paymentUnzerTransfer = $this->updatePaymentUnzerTransfer($unzerPaymentTransfer);
        $paymentUnzerOrderItemsCollection = $this->updatePaymentUnzerOrderItemCollection($unzerPaymentTransfer, $orderItemStatus, $filteredSalesOrderItemIds);
        $paymentUnzerTransactionCollection = $this->createPaymentUnzerTransactionCollection($unzerPaymentTransfer, $paymentUnzerTransfer);

        $this->unzerWriter->updateUnzerPaymentDetails(
            $paymentUnzerTransfer,
            $paymentUnzerOrderItemsCollection,
            $paymentUnzerTransactionCollection,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    protected function updatePaymentUnzerTransfer(UnzerPaymentTransfer $unzerPaymentTransfer): PaymentUnzerTransfer
    {
        $paymentUnzerTransfer = $this->unzerReader->getPaymentUnzerByOrderReference($unzerPaymentTransfer->getOrderId());

        return $this->unzerPaymentMapper
            ->mapUnzerPaymentTransferToPaymentUnzerTransfer($unzerPaymentTransfer, $paymentUnzerTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param string $omsStatus
     * @param array|null $filteredSalesOrderItemIds
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer
     */
    protected function updatePaymentUnzerOrderItemCollection(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        string $omsStatus,
        array $filteredSalesOrderItemIds = []
    ): PaymentUnzerOrderItemCollectionTransfer {
        $unzerPaymentOrderItemCollection = $this->unzerReader
            ->getPaymentUnzerOrderItemCollectionByOrderId($unzerPaymentTransfer->getOrderId());

        foreach ($unzerPaymentOrderItemCollection->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            if (count($filteredSalesOrderItemIds) !== 0 && in_array($paymentUnzerOrderItem->getIdSalesOrderItem(), $filteredSalesOrderItemIds, true)) {
                $paymentUnzerOrderItem->setStatus($omsStatus);

                continue;
            }

            if (count($filteredSalesOrderItemIds) === 0) {
                $paymentUnzerOrderItem->setStatus($omsStatus);
            }
        }

        return $unzerPaymentOrderItemCollection;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer
     */
    protected function createPaymentUnzerTransactionCollection(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer
    ): PaymentUnzerTransactionCollectionTransfer {
        $paymentUnzerTransactionCollectionTransfer = new PaymentUnzerTransactionCollectionTransfer();
        foreach ($unzerPaymentTransfer->getTransactions() as $unzerTransactionTransfer) {
            $paymentUnzerTransactionTransfer = $this->unzerPaymentMapper
                ->mapUnzerTransactionTransferToPaymentUnzerTransactionTransfer(
                    $unzerTransactionTransfer,
                    $unzerPaymentTransfer,
                    new PaymentUnzerTransactionTransfer(),
                );
            $paymentUnzerTransactionTransfer->setIdPaymentUnzer($paymentUnzerTransfer->getIdPaymentUnzer());

            $paymentUnzerTransactionCollectionTransfer->addPaymentUnzerTransaction($paymentUnzerTransactionTransfer);
        }

        return $paymentUnzerTransactionCollectionTransfer;
    }
}
