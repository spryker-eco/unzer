<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Mapper;

use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Generated\Shared\Transfer\UnzerTransactionTransfer;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerPaymentMapper implements UnzerPaymentMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapPaymentUnzerTransferToUnzerPaymentTransfer(
        PaymentUnzerTransfer $paymentUnzerTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer {
        $amountTotal = (int)$paymentUnzerTransfer->getAmountTotal() / UnzerConstants::INT_TO_FLOAT_DIVIDER;
        $amountRemaining = (int)$paymentUnzerTransfer->getAmountRemaining() / UnzerConstants::INT_TO_FLOAT_DIVIDER;
        $amountCharged = (int)$paymentUnzerTransfer->getAmountCharged() / UnzerConstants::INT_TO_FLOAT_DIVIDER;
        $amountCanceled = (int)$paymentUnzerTransfer->getAmountCanceled() / UnzerConstants::INT_TO_FLOAT_DIVIDER;

        $unzerPaymentTransfer
            ->setId($paymentUnzerTransfer->getPaymentId())
            ->setCurrency($paymentUnzerTransfer->getCurrency())
            ->setOrderId($paymentUnzerTransfer->getOrderId())
            ->setAmountTotal((int)$amountTotal)
            ->setAmountRemaining((int)$amountRemaining)
            ->setAmountCharged((int)$amountCharged)
            ->setAmountCanceled((int)$amountCanceled)
            ->setStateName($paymentUnzerTransfer->getState())
            ->setStateId($paymentUnzerTransfer->getStateId())
            ->setCustomer((new UnzerCustomerTransfer())->setId($paymentUnzerTransfer->getCustomerId()))
            ->setBasket((new UnzerBasketTransfer())->setId($paymentUnzerTransfer->getBasketId()))
            ->setPaymentResource((new UnzerPaymentResourceTransfer())->setId($paymentUnzerTransfer->getTypeId()))
            ->setIsAuthorizable($paymentUnzerTransfer->getIsAuthorizable())
            ->setIsMarketplace($paymentUnzerTransfer->getIsMarketplace())
            ->setUnzerKeypair((new UnzerKeypairTransfer())->setKeypairId($paymentUnzerTransfer->getKeypairId()));

        return $unzerPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    public function mapUnzerPaymentTransferToPaymentUnzerTransfer(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        PaymentUnzerTransfer $paymentUnzerTransfer
    ): PaymentUnzerTransfer {
        $paymentUnzerTransfer
            ->setAmountTotal((int)($unzerPaymentTransfer->getAmountTotal() * UnzerConstants::INT_TO_FLOAT_DIVIDER))
            ->setAmountRemaining((int)($unzerPaymentTransfer->getAmountRemaining() * UnzerConstants::INT_TO_FLOAT_DIVIDER))
            ->setAmountCanceled((int)($unzerPaymentTransfer->getAmountCanceled() * UnzerConstants::INT_TO_FLOAT_DIVIDER))
            ->setAmountCharged((int)($unzerPaymentTransfer->getAmountCharged() * UnzerConstants::INT_TO_FLOAT_DIVIDER))
            ->setState($unzerPaymentTransfer->getStateName())
            ->setStateId($unzerPaymentTransfer->getStateId())
            ->setCustomerId($unzerPaymentTransfer->getCustomerOrFail()->getId())
            ->setBasketId($unzerPaymentTransfer->getBasketOrFail()->getId())
            ->setPaymentId($unzerPaymentTransfer->getId())
            ->setCurrency($unzerPaymentTransfer->getCurrency())
            ->setTypeId($unzerPaymentTransfer->getPaymentResourceOrFail()->getId())
            ->setKeypairId($unzerPaymentTransfer->getUnzerKeypairOrFail()->getKeypairIdOrFail());

        return $paymentUnzerTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerTransactionTransfer $unzerTransactionTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransactionTransfer
     */
    public function mapUnzerTransactionTransferToPaymentUnzerTransactionTransfer(
        UnzerTransactionTransfer $unzerTransactionTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer,
        PaymentUnzerTransactionTransfer $paymentUnzerTransactionTransfer
    ): PaymentUnzerTransactionTransfer {
        return $paymentUnzerTransactionTransfer->fromArray($unzerTransactionTransfer->toArray(), true)
            ->setTransactionUniqueId($this->generateUniqueTransactionId($unzerTransactionTransfer, $unzerPaymentTransfer))
            ->setTransactionId($this->parseTransactionId($unzerTransactionTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerTransactionTransfer $unzerTransactionTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return string
     */
    protected function generateUniqueTransactionId(
        UnzerTransactionTransfer $unzerTransactionTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): string {
        return md5(
            $unzerPaymentTransfer->getIdOrFail() .
            $unzerTransactionTransfer->getTypeOrFail() .
            $unzerTransactionTransfer->getStatusOrFail() .
            (string)$unzerTransactionTransfer->getParticipantId(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerTransactionTransfer $unzerTransactionTransfer
     *
     * @return string
     */
    protected function parseTransactionId(UnzerTransactionTransfer $unzerTransactionTransfer): string
    {
        $urlParts = explode('/', $unzerTransactionTransfer->getUrlOrFail());

        return end($urlParts);
    }
}
