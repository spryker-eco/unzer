<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Saver;

use Exception;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\MerchantUnzerParticipantTransfer;
use Generated\Shared\Transfer\PaymentUnzerOrderItemCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionCollectionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as UnzerSharedConfig;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

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
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Business\Writer\UnzerWriterInterface $unzerWriter
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface $unzerPaymentMapper
     */
    public function __construct(
        UnzerReaderInterface $unzerReader,
        UnzerWriterInterface $unzerWriter,
        UnzerConfig $unzerConfig,
        UnzerPaymentMapperInterface $unzerPaymentMapper
    ) {
        $this->unzerReader = $unzerReader;
        $this->unzerWriter = $unzerWriter;
        $this->unzerConfig = $unzerConfig;
        $this->unzerPaymentMapper = $unzerPaymentMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @throws \Exception
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void
    {
        if ($quoteTransfer->getPayment()->getPaymentProvider() !== UnzerSharedConfig::PROVIDER_NAME) {
            return;
        }

        $paymentUnzerTransfer = $this->unzerReader->getPaymentUnzerByOrderReference($saveOrderTransfer->getOrderReference());
        if ($paymentUnzerTransfer->getIdPaymentUnzer() !== null) {
            //@todo refactor
            throw new Exception('order already exists!');
        }

        $this->unzerWriter->saveUnzerPaymentEntities($quoteTransfer, $saveOrderTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param string $orderItemStatus
     * @param array|null $filteredSalesOrderItemIds
     *
     * @return void
     */
    public function savePaymentEntities(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        string $orderItemStatus,
        ?array $filteredSalesOrderItemIds = null
    ): void {
        $paymentUnzerTransfer = $this->updatePaymentUnzerTransfer($unzerPaymentTransfer);
        $paymentUnzerOrderItemsCollection = $this->updatePaymentUnzerOrderItemCollection($unzerPaymentTransfer, $orderItemStatus, $filteredSalesOrderItemIds);
        $paymentUnzerTransactionCollection = $this->preparePaymentUnzerTransactionCollection($unzerPaymentTransfer, $paymentUnzerTransfer);

        $this->unzerWriter->updateUnzerPaymentEntities(
            $paymentUnzerTransfer,
            $paymentUnzerOrderItemsCollection,
            $paymentUnzerTransactionCollection
        );
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function saveMerchantUnzerParticipantByMerchant(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        if ($merchantTransfer->getMerchantUnzerParticipantId()) {
            $merchantUnzerParticipantTransfer = (new MerchantUnzerParticipantTransfer())
                ->setMerchantId($merchantTransfer->getIdMerchant())
                ->setParticipantId($merchantTransfer->getMerchantUnzerParticipantId());

            $this->unzerWriter->saveMerchantUnzerParticipantEntity($merchantUnzerParticipantTransfer);
        }

        return (new MerchantResponseTransfer())
            ->setIsSuccess(true)
            ->setMerchant($merchantTransfer);
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
        ?array $filteredSalesOrderItemIds = null
    ): PaymentUnzerOrderItemCollectionTransfer {
        $unzerPaymentOrderItemCollection = $this->unzerReader
            ->getPaymentUnzerOrderItemCollectionByOrderReference($unzerPaymentTransfer->getOrderId());

        foreach ($unzerPaymentOrderItemCollection->getPaymentUnzerOrderItems() as $paymentUnzerOrderItem) {
            if ($filteredSalesOrderItemIds !== null && in_array($paymentUnzerOrderItem->getIdSalesOrderItem(), $filteredSalesOrderItemIds, true)) {
                $paymentUnzerOrderItem->setStatus($omsStatus);

                continue;
            }

            if ($filteredSalesOrderItemIds === null) {
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
    protected function preparePaymentUnzerTransactionCollection(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer
    ): PaymentUnzerTransactionCollectionTransfer {
        $paymentUnzerTransactionCollectionTransfer = new PaymentUnzerTransactionCollectionTransfer();
        foreach ($unzerPaymentTransfer->getTransactions() as $unzerTransactionTransfer) {
            $paymentUnzerTransactionTransfer = $this->unzerPaymentMapper
                ->mapUnzerTransactionTransferToPaymentUnzerTransactionTransfer(
                    $unzerTransactionTransfer,
                    $unzerPaymentTransfer,
                    new PaymentUnzerTransactionTransfer()
                );
            $paymentUnzerTransactionTransfer->setIdPaymentUnzer($paymentUnzerTransfer->getIdPaymentUnzer());

            $paymentUnzerTransactionCollectionTransfer->addPaymentUnzerTransaction($paymentUnzerTransactionTransfer);
        }

        return $paymentUnzerTransactionCollectionTransfer;
    }
}
