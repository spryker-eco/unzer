<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerRefundTransfer;

interface UnzerRefundAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerRefundTransfer $unzerRefundTransfer
     *
     * @return void
     */
    public function refundPayment(UnzerRefundTransfer $unzerRefundTransfer): void;
}
