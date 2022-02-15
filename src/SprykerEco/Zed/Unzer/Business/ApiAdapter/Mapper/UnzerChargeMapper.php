<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiChargeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiChargeResponseTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerChargeTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\UnzerConfig;
use SprykerEco\Zed\Unzer\UnzerConstants;

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
        return $unzerApiChargeRequestTransfer
            ->setPaymentId($unzerPaymentTransfer->getId())
            ->setCustomerId($unzerPaymentTransfer->getCustomerOrFail()->getId())
            ->setBasketId($unzerPaymentTransfer->getBasketOrFail()->getId())
            ->setTypeId($unzerPaymentTransfer->getPaymentResourceOrFail()->getId())
            ->setReturnUrl($this->unzerConfig->getChargeReturnUrl())
            ->setAmount($unzerPaymentTransfer->getAmountTotal())
            ->setCurrency($unzerPaymentTransfer->getCurrency())
            ->setOrderId($unzerPaymentTransfer->getOrderId())
            ->setInvoiceId($unzerPaymentTransfer->getInvoiceId())
            ->setPaymentReference($unzerPaymentTransfer->getOrderId());
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
        return $unzerPaymentTransfer->setStateId($unzerApiChargeResponseTransfer->getStateId());
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
        return $unzerPaymentTransfer->setId($unzerApiChargeResponseTransfer->getPaymentId())
            ->setAmountTotal((int)$unzerApiChargeResponseTransfer->getAmount() * UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setCurrency($unzerApiChargeResponseTransfer->getCurrency())
            ->setRedirectUrl($unzerApiChargeResponseTransfer->getRedirectUrl())
            ->setCustomer((new UnzerCustomerTransfer())->setId($unzerApiChargeResponseTransfer->getCustomerId()))
            ->setBasket((new UnzerBasketTransfer())->setId($unzerApiChargeResponseTransfer->getBasketId()))
            ->setPaymentResource((new UnzerPaymentResourceTransfer())->setId($unzerApiChargeResponseTransfer->getTypeId()))
            ->setRedirectUrl($unzerApiChargeResponseTransfer->getRedirectUrl());
    }

    /**
     * @param UnzerChargeTransfer $unzerChargeTransfer
     * @param UnzerApiChargeRequestTransfer $unzerApiChargeRequestTransfer
     *
     * @return UnzerApiChargeRequestTransfer
     */
    public function mapUnzerChargeTransferToUnzerApiChargeRequestTransfer(
        UnzerChargeTransfer $unzerChargeTransfer,
        UnzerApiChargeRequestTransfer $unzerApiChargeRequestTransfer
    ): UnzerApiChargeRequestTransfer
    {
        return $unzerApiChargeRequestTransfer
            ->fromArray($unzerChargeTransfer->toArray(), true)
            ->setAmount($unzerChargeTransfer->getAmount() / UnzerConstants::INT_TO_FLOAT_DIVIDER);
    }
}
