<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\OmsStateResolver;

use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerOmsStateResolverInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return string
     */
    public function getUnzerPaymentOmsStatus(UnzerPaymentTransfer $unzerPaymentTransfer): string;
}
