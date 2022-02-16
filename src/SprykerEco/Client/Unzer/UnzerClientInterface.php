<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\Unzer;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerClientInterface
{
    /**
     * Specification:
     *  - Makes Zed request.
     *  - Processes Unzer notification and modifies payment properties.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerNotificationTransfer $unzerNotificationTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerNotificationTransfer
     */
    public function processNotification(UnzerNotificationTransfer $unzerNotificationTransfer): UnzerNotificationTransfer;

    /**
     * Specification:
     * - Makes Zed request.
     * - Gets updated Unzer payment info by `OrderTransfer.orderReference`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function findUpdatedUnzerPaymentForOrderAction(OrderTransfer $orderTransfer): UnzerPaymentTransfer;
}
