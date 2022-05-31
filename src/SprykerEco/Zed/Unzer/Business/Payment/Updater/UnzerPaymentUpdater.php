<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Updater;

use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerPaymentUpdater implements UnzerPaymentUpdaterInterface
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
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param string $orderItemStatus
     * @param array<int> $filteredSalesOrderItemIds
     *
     * @return void
     */
    public function updateUnzerPaymentDetails(
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
        $paymentUnzerTransfer = $this->unzerReader->getPaymentUnzerByOrderReference($unzerPaymentTransfer->getOrderIdOrFail());

        return $this->unzerPaymentMapper
            ->mapUnzerPaymentTransferToPaymentUnzerTransfer($unzerPaymentTransfer, $paymentUnzerTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param string $omsStatus
     * @param array<int> $filteredSalesOrderItemIds
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer
     */
    protected function updatePaymentUnzerOrderItemCollection(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        string $omsStatus,
        array $filteredSalesOrderItemIds = []
    ): PaymentUnzerOrderItemCollectionTransfer {
        $unzerPaymentOrderItemCollection = $this->unzerReader
            ->getPaymentUnzerOrderItemCollectionByOrderId($unzerPaymentTransfer->getOrderIdOrFail());

        if ($this->isAuthorizableFullOrderCompletedUpdate($unzerPaymentTransfer, $filteredSalesOrderItemIds, $omsStatus)) {
            return $unzerPaymentOrderItemCollection;
        }

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

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param array<int> $filteredSalesOrderItemIds
     * @param string $omsStatus
     *
     * @return bool
     */
    protected function isAuthorizableFullOrderCompletedUpdate(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        array $filteredSalesOrderItemIds,
        string $omsStatus
    ): bool {
        return count($filteredSalesOrderItemIds) === 0
            && $omsStatus === UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED
            && $unzerPaymentTransfer->getIsAuthorizableOrFail();
    }
}
