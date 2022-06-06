<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Updater;

use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerPaymentUpdaterInterface
{
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
    ): void;
}
