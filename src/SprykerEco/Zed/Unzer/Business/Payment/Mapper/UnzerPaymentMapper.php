<?php

namespace SprykerEco\Zed\Unzer\Business\Payment\Mapper;

use Generated\Shared\Transfer\PaymentUnzerTransactionTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Generated\Shared\Transfer\UnzerTransactionTransfer;

class UnzerPaymentMapper implements UnzerPaymentMapperInterface
{
    protected const INT_FLOAT_COEF = 100;

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
            ->setAmountTotal($paymentUnzerTransfer->getAmountTotal() / static::INT_FLOAT_COEF)
            ->setAmountRemaining($paymentUnzerTransfer->getAmountRemaining() / static::INT_FLOAT_COEF)
            ->setAmountCharged($paymentUnzerTransfer->getAmountCharged() / static::INT_FLOAT_COEF)
            ->setAmountCanceled($paymentUnzerTransfer->getAmountCanceled() / static::INT_FLOAT_COEF)
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
            ->setAmountTotal($unzerPaymentTransfer->getAmountTotal() * static::INT_FLOAT_COEF)
            ->setAmountRemaining($unzerPaymentTransfer->getAmountRemaining() * static::INT_FLOAT_COEF)
            ->setAmountCanceled($unzerPaymentTransfer->getAmountCanceled() * static::INT_FLOAT_COEF)
            ->setAmountCharged($unzerPaymentTransfer->getAmountCharged() * static::INT_FLOAT_COEF)
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
        $paymentUnzerTransactionTransfer->fromArray($unzerTransactionTransfer->toArray(), true);
        $paymentUnzerTransactionTransfer->setTransactionUniqueId(
            $this->generateUniqueTransactionId($unzerTransactionTransfer, $unzerPaymentTransfer)
        );
        $paymentUnzerTransactionTransfer->setTransactionId(
            $this->parseTransactionId($unzerTransactionTransfer)
        );

        return $paymentUnzerTransactionTransfer;
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

        return end($urlParts);
    }
}
