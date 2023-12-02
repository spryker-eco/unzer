<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\OmsStateResolver;

use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\UnzerConfig;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerOmsStateResolver implements UnzerOmsStateResolverInterface
{
    protected UnzerConfig $unzerConfig;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(UnzerConfig $unzerConfig)
    {
        $this->unzerConfig = $unzerConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return string
     */
    public function getUnzerPaymentOmsStatus(UnzerPaymentTransfer $unzerPaymentTransfer): string
    {
        $chargeUnzerTransactionTransfer = null;
        $authorizeUnzerTransactionTransfer = null;
        foreach ($unzerPaymentTransfer->getTransactions() as $unzerTransactionTransfer) {
            if ($unzerTransactionTransfer->getTypeOrFail() === UnzerConstants::TRANSACTION_TYPE_CHARGE) {
                $chargeUnzerTransactionTransfer = $unzerTransactionTransfer;

                continue;
            }

            if ($unzerTransactionTransfer->getTypeOrFail() === UnzerConstants::TRANSACTION_TYPE_AUTHORIZE) {
                $authorizeUnzerTransactionTransfer = $unzerTransactionTransfer;
            }
        }

        if ($chargeUnzerTransactionTransfer !== null) {
            return $this->unzerConfig->mapUnzerChargePaymentStatusToOmsStatus($chargeUnzerTransactionTransfer->getStatusOrFail());
        }

        if ($authorizeUnzerTransactionTransfer !== null) {
            return $this->unzerConfig->mapUnzerAuthorizePaymentStatusToOmsStatus($authorizeUnzerTransactionTransfer->getStatusOrFail());
        }

        return $this->unzerConfig->mapUnzerPaymentStatusToOmsStatus($unzerPaymentTransfer->getStateIdOrFail());
    }
}
