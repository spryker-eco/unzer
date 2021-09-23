<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiChargeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiChargeResponseTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerChargeMapper implements UnzerChargeMapperInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(UnzerConfig $unzerConfig)
    {
        $this->unzerConfig = $unzerConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\UnzerApiChargeRequestTransfer $unzerApiChargeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiChargeRequestTransfer
     */
    public function mapUnzerPaymentTransferToUnzerApiChargeRequestTransfer(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        UnzerApiChargeRequestTransfer $unzerApiChargeRequestTransfer
    ): UnzerApiChargeRequestTransfer {
        $unzerApiChargeRequestTransfer
            ->setPaymentId($unzerPaymentTransfer->getId())
            ->setCustomerId($unzerPaymentTransfer->getCustomer()->getId())
            ->setBasketId($unzerPaymentTransfer->getBasket()->getId())
            ->setTypeId($unzerPaymentTransfer->getPaymentResource()->getId())
            ->setReturnUrl($this->unzerConfig->getChargeReturnUrl())
            ->setAmount($unzerPaymentTransfer->getAmountTotal())
            ->setCurrency($unzerPaymentTransfer->getCurrency())
            ->setOrderId($unzerPaymentTransfer->getOrderId())
            ->setInvoiceId($unzerPaymentTransfer->getInvoiceId())
            ->setPaymentReference($unzerPaymentTransfer->getOrderId());

            return $unzerApiChargeRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapAuthorizableUnzerApiChargeResponseTransferToUnzerPaymentTransfer(
        UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer {
        //Only stateId should be mapped
        $unzerPaymentTransfer->setStateId($unzerApiChargeResponseTransfer->getStateId());

        return $unzerPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapUnzerApiChargeResponseTransferToUnzerPaymentTransfer(
        UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer {
        $unzerPaymentTransfer->setId($unzerApiChargeResponseTransfer->getPaymentId())
            ->setAmountTotal((int)$unzerApiChargeResponseTransfer->getAmount() * 100)
            ->setCurrency($unzerApiChargeResponseTransfer->getCurrency())
            ->setRedirectUrl($unzerApiChargeResponseTransfer->getRedirectUrl())
            ->setCustomer(
                (new UnzerCustomerTransfer())->setId($unzerApiChargeResponseTransfer->getCustomerId())
            )
            ->setBasket(
                (new UnzerBasketTransfer())->setId($unzerApiChargeResponseTransfer->getBasketId())
            )
            ->setPaymentResource(
                (new UnzerPaymentResourceTransfer())->setId($unzerApiChargeResponseTransfer->getTypeId())
            )
            ->setRedirectUrl($unzerApiChargeResponseTransfer->getRedirectUrl());

        return $unzerPaymentTransfer;
    }
}
