<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer;
use Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeResponseTransfer;
use Generated\Shared\Transfer\UnzerBasketTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerAuthorizePaymentMapper implements UnzerAuthorizePaymentMapperInterface
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
     * @param \Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer $unzerApiMarketplaceAuthorizeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiMarketplaceAuthorizeRequestTransfer
     */
    public function mapUnzerPaymentTransferToUnzerApiMarketplaceAuthorizeRequestTransfer(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        UnzerApiMarketplaceAuthorizeRequestTransfer $unzerApiMarketplaceAuthorizeRequestTransfer
    ): UnzerApiMarketplaceAuthorizeRequestTransfer {
        return $unzerApiMarketplaceAuthorizeRequestTransfer->fromArray($unzerPaymentTransfer->toArray(), true)
            ->setAmount($unzerPaymentTransfer->getAmountTotal())
            ->setPaymentReference($unzerPaymentTransfer->getOrderId())
            ->setTypeId($unzerPaymentTransfer->getPaymentResource()->getId())
            ->setCustomerId($unzerPaymentTransfer->getCustomer()->getId())
            ->setBasketId($unzerPaymentTransfer->getBasket()->getId())
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
            ->setAmountTotal((int)($unzerApiMarketplaceAuthorizeResponseTransfer->getAmount() * 100))
            ->setCurrency($unzerApiMarketplaceAuthorizeResponseTransfer->getCurrency())
            ->setRedirectUrl($unzerApiMarketplaceAuthorizeResponseTransfer->getRedirectUrl())
            ->setCustomer((new UnzerCustomerTransfer())->setId($unzerApiMarketplaceAuthorizeResponseTransfer->getCustomerId()))
            ->setBasket((new UnzerBasketTransfer())->setId($unzerApiMarketplaceAuthorizeResponseTransfer->getBasketId()))
            ->setPaymentResource((new UnzerPaymentResourceTransfer())->setId($unzerApiMarketplaceAuthorizeResponseTransfer->getTypeId()))
            ->setRedirectUrl($unzerApiMarketplaceAuthorizeResponseTransfer->getRedirectUrl());
    }
}