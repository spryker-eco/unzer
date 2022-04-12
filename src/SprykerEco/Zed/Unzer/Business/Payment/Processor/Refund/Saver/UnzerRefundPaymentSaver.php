<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\Refund\Saver;

use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerRefundPaymentSaver implements UnzerRefundPaymentSaverInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface
     */
    protected $unzerPaymentAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface
     */
    protected $unzerPaymentSaver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface $unzerPaymentMapper
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface $unzerPaymentSaver
     */
    public function __construct(
        UnzerPaymentMapperInterface $unzerPaymentMapper,
        UnzerPaymentAdapterInterface $unzerPaymentAdapter,
        UnzerPaymentSaverInterface $unzerPaymentSaver
    ) {
        $this->unzerPaymentMapper = $unzerPaymentMapper;
        $this->unzerPaymentAdapter = $unzerPaymentAdapter;
        $this->unzerPaymentSaver = $unzerPaymentSaver;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     * @param array<int> $salesOrderItemIds
     *
     * @return void
     */
    public function saveUnzerPaymentDetails(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer,
        array $salesOrderItemIds
    ): void {
        $unzerPaymentTransfer = $this->unzerPaymentMapper
            ->mapPaymentUnzerTransferToUnzerPaymentTransfer($paymentUnzerTransfer, new UnzerPaymentTransfer());
        $unzerPaymentTransfer->setUnzerKeypair($unzerKeypairTransfer);
        $unzerPaymentTransfer = $this->unzerPaymentAdapter->getPaymentInfo($unzerPaymentTransfer);

        $this->unzerPaymentSaver->saveUnzerPaymentDetails(
            $unzerPaymentTransfer,
            UnzerConstants::OMS_STATUS_CHARGE_REFUNDED,
            $salesOrderItemIds,
        );
    }
}
