<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\DirectCharge;

use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerDirectChargeProcessorInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function charge(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer;
}
