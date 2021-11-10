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
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Generated\Shared\Transfer\UnzerTransactionTransfer;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerPaymentMapper implements UnzerPaymentMapperInterface
{
    /**
     * @var int
     */
    protected const INT_TO_FLOAT_DIVIDER = 100;

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
        $unzerPaymentTransfer
            ->setId($paymentUnzerTransfer->getPaymentId())
            ->setCurrency($paymentUnzerTransfer->getCurrency())
            ->setOrderId($paymentUnzerTransfer->getOrderId())
            ->setAmountTotal($paymentUnzerTransfer->getAmountTotal() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setAmountRemaining($paymentUnzerTransfer->getAmountRemaining() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setAmountCharged($paymentUnzerTransfer->getAmountCharged() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setAmountCanceled($paymentUnzerTransfer->getAmountCanceled() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setStateName($paymentUnzerTransfer->getState())
            ->setStateId($paymentUnzerTransfer->getStateId())
            ->setCustomer((new UnzerCustomerTransfer())->setId($paymentUnzerTransfer->getCustomerId()))
            ->setBasket((new UnzerBasketTransfer())->setId($paymentUnzerTransfer->getBasketId()))
            ->setPaymentResource((new UnzerPaymentResourceTransfer())->setId($paymentUnzerTransfer->getTypeId()))
            ->setIsAuthorizable($paymentUnzerTransfer->getIsAuthorizable())
            ->setIsMarketplace($paymentUnzerTransfer->getIsMarketplace());

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
            ->setAmountTotal($unzerPaymentTransfer->getAmountTotal() * UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setAmountRemaining($unzerPaymentTransfer->getAmountRemaining() * UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setAmountCanceled($unzerPaymentTransfer->getAmountCanceled() * UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setAmountCharged($unzerPaymentTransfer->getAmountCharged() * UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setState($unzerPaymentTransfer->getStateName())
            ->setStateId($unzerPaymentTransfer->getStateId())
            ->setCustomerId($unzerPaymentTransfer->getCustomer()->getId())
            ->setBasketId($unzerPaymentTransfer->getBasket()->getId())
            ->setPaymentId($unzerPaymentTransfer->getId())
            ->setCurrency($unzerPaymentTransfer->getCurrency())
            ->setTypeId($unzerPaymentTransfer->getPaymentResource()->getId());

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
            $unzerPaymentTransfer->getId() .
            $unzerTransactionTransfer->getType() .
            $unzerTransactionTransfer->getStatus() .
            $unzerTransactionTransfer->getParticipantId()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerTransactionTransfer $unzerTransactionTransfer
     *
     * @return string
     */
    protected function parseTransactionId(UnzerTransactionTransfer $unzerTransactionTransfer): string
    {
        $url = $unzerTransactionTransfer->getUrl();
        $urlParts = explode('/', $url);

        return (string)end($urlParts);
    }
}
