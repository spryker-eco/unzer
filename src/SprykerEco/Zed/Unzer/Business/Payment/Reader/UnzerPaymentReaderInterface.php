<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Reader;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerPaymentReaderInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer|null
     */
    public function findUnzerPaymentForOrder(OrderTransfer $orderTransfer): ?UnzerPaymentTransfer;
}
