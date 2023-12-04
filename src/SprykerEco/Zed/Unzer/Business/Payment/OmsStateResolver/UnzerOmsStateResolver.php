<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\OmsStateResolver;

use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\UnzerConfig;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerOmsStateResolver implements UnzerOmsStateResolverInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
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
            return $this->mapUnzerPaymentTransactionStatusToOmsStatus(
                $this->unzerConfig->getUnzerChargePaymentStatusToOmsStatusMap(),
                $chargeUnzerTransactionTransfer->getStatusOrFail(),
            );
        }

        if ($authorizeUnzerTransactionTransfer !== null) {
            return $this->mapUnzerPaymentTransactionStatusToOmsStatus(
                $this->unzerConfig->getUnzerAuthorizePaymentStatusToOmsStatusMap(),
                $authorizeUnzerTransactionTransfer->getStatusOrFail(),
            );
        }

        return $this->mapUnzerPaymentStateToOmsStatus($unzerPaymentTransfer->getStateIdOrFail());
    }

    /**
     * @param array<string, string> $unzerPaymentStatusToOmsStatusMap
     * @param string $unzerPaymentStatus
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return string
     */
    protected function mapUnzerPaymentTransactionStatusToOmsStatus(array $unzerPaymentStatusToOmsStatusMap, string $unzerPaymentStatus): string
    {
        if (!isset($unzerPaymentStatusToOmsStatusMap[$unzerPaymentStatus])) {
            $message = sprintf('Undefined payment transaction status provided by Unzer: %s', $unzerPaymentStatus);

            throw new UnzerException($message);
        }

        return $unzerPaymentStatusToOmsStatusMap[$unzerPaymentStatus];
    }

    /**
     * @param int $unzerPaymentStateId
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return string
     */
    protected function mapUnzerPaymentStateToOmsStatus(int $unzerPaymentStateId): string
    {
        $paymentStateToOmsStatusMap = $this->unzerConfig->getUnzerPaymentStateToOmsStatusMap();
        if (!isset($paymentStateToOmsStatusMap[$unzerPaymentStateId])) {
            $message = sprintf('Undefined payment state provided by Unzer: %s', $unzerPaymentStateId);

            throw new UnzerException($message);
        }

        return $paymentStateToOmsStatusMap[$unzerPaymentStateId];
    }
}
