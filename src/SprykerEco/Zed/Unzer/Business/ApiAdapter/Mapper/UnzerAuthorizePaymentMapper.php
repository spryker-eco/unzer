<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiAuthorizeResponseTransfer;
use Generated\Shared\Transfer\UnzerApiErrorResponseTransfer;
use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeResponseTransfer;
use Generated\Shared\Transfer\UnzerApiResponseErrorTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerPaymentErrorTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\UnzerConfig;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerAuthorizePaymentMapper implements UnzerAuthorizePaymentMapperInterface
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
     * @param \Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer $unzerApiMarketplaceAuthorizeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer
     */
    public function mapUnzerPaymentTransferToUnzerApiMarketplaceAuthorizeRequestTransfer(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        UnzerApiMarketplaceAuthorizeRequestTransfer $unzerApiMarketplaceAuthorizeRequestTransfer
    ): UnzerApiMarketplaceAuthorizeRequestTransfer {
        return $unzerApiMarketplaceAuthorizeRequestTransfer->fromArray($unzerPaymentTransfer->toArray(), true)
            ->setAmount($unzerPaymentTransfer->getAmountTotal() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setPaymentReference($unzerPaymentTransfer->getOrderId())
            ->setTypeId($unzerPaymentTransfer->getPaymentResourceOrFail()->getId())
            ->setCustomerId($unzerPaymentTransfer->getCustomerOrFail()->getId())
            ->setBasketId($unzerPaymentTransfer->getBasketOrFail()->getId())
            ->setReturnUrl($this->unzerConfig->getAuthorizeReturnUrl());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\UnzerApiAuthorizeRequestTransfer $unzerApiAuthorizeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiAuthorizeRequestTransfer
     */
    public function mapUnzerPaymentTransferToUnzerApiAuthorizeRequestTransfer(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        UnzerApiAuthorizeRequestTransfer $unzerApiAuthorizeRequestTransfer
    ): UnzerApiAuthorizeRequestTransfer {
        return $unzerApiAuthorizeRequestTransfer->fromArray($unzerPaymentTransfer->toArray(), true)
            ->setAmount($unzerPaymentTransfer->getAmountTotal() / UnzerConstants::INT_TO_FLOAT_DIVIDER)
            ->setTypeId($unzerPaymentTransfer->getPaymentResourceOrFail()->getId())
            ->setCustomerId($unzerPaymentTransfer->getCustomerOrFail()->getId())
            ->setReturnUrl($this->unzerConfig->getAuthorizeReturnUrl());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeResponseTransfer $unzerApiMarketplaceAuthorizeResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapUnzerApiMarketplaceAuthorizeResponseTransferToUnzerPaymentTransfer(
        UnzerApiMarketplaceAuthorizeResponseTransfer $unzerApiMarketplaceAuthorizeResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer {
        return $unzerPaymentTransfer
            ->setId($unzerApiMarketplaceAuthorizeResponseTransfer->getPaymentId())
            ->setAmountTotal((int)((float)$unzerApiMarketplaceAuthorizeResponseTransfer->getAmount() * UnzerConstants::INT_TO_FLOAT_DIVIDER))
            ->setCurrency($unzerApiMarketplaceAuthorizeResponseTransfer->getCurrency())
            ->setRedirectUrl($unzerApiMarketplaceAuthorizeResponseTransfer->getRedirectUrl())
            ->setCustomer((new UnzerCustomerTransfer())->setId($unzerApiMarketplaceAuthorizeResponseTransfer->getCustomerId()))
            ->setBasket((new UnzerBasketTransfer())->setId($unzerApiMarketplaceAuthorizeResponseTransfer->getBasketId()))
            ->setPaymentResource((new UnzerPaymentResourceTransfer())->setId($unzerApiMarketplaceAuthorizeResponseTransfer->getTypeId()))
            ->setRedirectUrl($unzerApiMarketplaceAuthorizeResponseTransfer->getRedirectUrl());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiAuthorizeResponseTransfer $unzerApiAuthorizeResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapUnzerApiAuthorizeResponseTransferToUnzerPaymentTransfer(
        UnzerApiAuthorizeResponseTransfer $unzerApiAuthorizeResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer {
        return $unzerPaymentTransfer
            ->setId($unzerApiAuthorizeResponseTransfer->getPaymentId())
            ->setAmountTotal((int)($unzerApiAuthorizeResponseTransfer->getAmount() * UnzerConstants::INT_TO_FLOAT_DIVIDER))
            ->setCurrency($unzerApiAuthorizeResponseTransfer->getCurrency())
            ->setRedirectUrl($unzerApiAuthorizeResponseTransfer->getRedirectUrl())
            ->setCustomer((new UnzerCustomerTransfer())->setId($unzerApiAuthorizeResponseTransfer->getCustomerId()))
            ->setPaymentResource((new UnzerPaymentResourceTransfer())->setId($unzerApiAuthorizeResponseTransfer->getTypeId()))
            ->setRedirectUrl($unzerApiAuthorizeResponseTransfer->getRedirectUrl());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapUnzerApiErrorResponseTransferToUnzerPaymentTransfer(
        UnzerApiErrorResponseTransfer $unzerApiErrorResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer {
        foreach ($unzerApiErrorResponseTransfer->getErrors() as $unzerApiResponseErrorTransfer) {
            $unzerPaymentErrorTransfer = $this->mapUnzerApiResponseErrorTransferToUnzerPaymentErrorTransfer(
                $unzerApiResponseErrorTransfer,
                new UnzerPaymentErrorTransfer(),
            );

            $unzerPaymentTransfer->addError($unzerPaymentErrorTransfer);
        }

        return $unzerPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiResponseErrorTransfer $unzerApiResponseErrorTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentErrorTransfer $unzerPaymentErrorTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentErrorTransfer
     */
    protected function mapUnzerApiResponseErrorTransferToUnzerPaymentErrorTransfer(
        UnzerApiResponseErrorTransfer $unzerApiResponseErrorTransfer,
        UnzerPaymentErrorTransfer $unzerPaymentErrorTransfer
    ): UnzerPaymentErrorTransfer {
        return $unzerPaymentErrorTransfer
            ->setMessage($unzerApiResponseErrorTransfer->getCustomerMessage())
            ->setErrorCode((int)$unzerApiResponseErrorTransfer->getCode());
    }
}
