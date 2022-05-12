<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Saver;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerPaymentSaverInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param string $orderItemStatus
     * @param array<int> $filteredSalesOrderItemIds
     *
     * @return void
     */
    public function saveUnzerPaymentDetails(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        string $orderItemStatus,
        array $filteredSalesOrderItemIds = []
    ): void;
}
