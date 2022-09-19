<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiChargeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiChargeResponseTransfer;
use Generated\Shared\Transfer\UnzerApiErrorResponseTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerChargeTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerPaymentErrorTransfer;
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
            ->setAmount($unzerPaymentTransfer->getAmountTotal() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
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
        $unzerCustomerTransfer = $this->mapUnzerApiChargeResponseTransferToUnzerCustomerTransfer(
            $unzerApiChargeResponseTransfer,
            $unzerPaymentTransfer->getCustomer() ?? new UnzerCustomerTransfer(),
        );

        $unzerBasketTransfer = $this->mapUnzerApiChargeResponseTransferToUnzerBasketTransfer(
            $unzerApiChargeResponseTransfer,
            $unzerPaymentTransfer->getBasket() ?? new UnzerBasketTransfer(),
        );

        $unzerPaymentResourceTransfer = $this->mapUnzerApiChargeResponseTransferToUnzerPaymentResourceTransfer(
            $unzerApiChargeResponseTransfer,
            $unzerPaymentTransfer->getPaymentResource() ?? new UnzerPaymentResourceTransfer(),
        );

        return $unzerPaymentTransfer->setId($unzerApiChargeResponseTransfer->getPaymentId())
            ->setAmountTotal((int)$unzerApiChargeResponseTransfer->getAmount() * UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setCurrency($unzerApiChargeResponseTransfer->getCurrency())
            ->setRedirectUrl($unzerApiChargeResponseTransfer->getRedirectUrl())
            ->setCustomer($unzerCustomerTransfer)
            ->setBasket($unzerBasketTransfer)
            ->setPaymentResource($unzerPaymentResourceTransfer)
            ->setRedirectUrl($unzerApiChargeResponseTransfer->getRedirectUrl());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerChargeTransfer $unzerChargeTransfer
     * @param \Generated\Shared\Transfer\UnzerApiChargeRequestTransfer $unzerApiChargeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiChargeRequestTransfer
     */
    public function mapUnzerChargeTransferToUnzerApiChargeRequestTransfer(
        UnzerChargeTransfer $unzerChargeTransfer,
        UnzerApiChargeRequestTransfer $unzerApiChargeRequestTransfer
    ): UnzerApiChargeRequestTransfer {
        return $unzerApiChargeRequestTransfer
            ->fromArray($unzerChargeTransfer->toArray(), true)
            ->setAmount($unzerChargeTransfer->getAmount() / UnzerConstants::INT_TO_FLOAT_DIVIDER);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiErrorResponseTransfer|null $unzerApiErrorResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapUnzerApiErrorResponseTransferToUnzerPaymentTransfer(
        ?UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer {
        if (!$unzerApiErrorResponseTransfer) {
            return $unzerPaymentTransfer;
        }

        foreach ($unzerApiErrorResponseTransfer->getErrors() as $unzerApiResponseErrorTransfer) {
            $unzerPaymentTransfer->addError(
                (new UnzerPaymentErrorTransfer())
                    ->setMessage($unzerApiResponseErrorTransfer->getCustomerMessage())
                    ->setErrorCode((int)$unzerApiResponseErrorTransfer->getCode()),
            );
        }

        return $unzerPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    protected function mapUnzerApiChargeResponseTransferToUnzerCustomerTransfer(
        UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer,
        UnzerCustomerTransfer $unzerCustomerTransfer
    ): UnzerCustomerTransfer {
        if (!$unzerApiChargeResponseTransfer->getCustomerId()) {
            return $unzerCustomerTransfer;
        }

        return $unzerCustomerTransfer->setId($unzerApiChargeResponseTransfer->getCustomerIdOrFail());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerBasketTransfer $unzerBasketTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerBasketTransfer
     */
    protected function mapUnzerApiChargeResponseTransferToUnzerBasketTransfer(
        UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer,
        UnzerBasketTransfer $unzerBasketTransfer
    ): UnzerBasketTransfer {
        if (!$unzerApiChargeResponseTransfer->getBasketId()) {
            return $unzerBasketTransfer;
        }

        return $unzerBasketTransfer->setId($unzerApiChargeResponseTransfer->getBasketIdOrFail());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentResourceTransfer
     */
    protected function mapUnzerApiChargeResponseTransferToUnzerPaymentResourceTransfer(
        UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer,
        UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer
    ): UnzerPaymentResourceTransfer {
        if (!$unzerApiChargeResponseTransfer->getTypeId()) {
            return $unzerPaymentResourceTransfer;
        }

        return $unzerPaymentResourceTransfer->setId($unzerApiChargeResponseTransfer->getTypeIdOrFail());
    }
}
