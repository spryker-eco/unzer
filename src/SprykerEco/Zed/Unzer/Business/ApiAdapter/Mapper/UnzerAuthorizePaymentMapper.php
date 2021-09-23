<?php

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
        $unzerApiMarketplaceAuthorizeRequestTransfer->setAmount($unzerPaymentTransfer->getAmountTotal());
        $unzerApiMarketplaceAuthorizeRequestTransfer->setCurrency($unzerPaymentTransfer->getCurrency());
        $unzerApiMarketplaceAuthorizeRequestTransfer->setOrderId($unzerPaymentTransfer->getOrderId());
        $unzerApiMarketplaceAuthorizeRequestTransfer->setInvoiceId($unzerPaymentTransfer->getInvoiceId());
        $unzerApiMarketplaceAuthorizeRequestTransfer->setPaymentReference($unzerPaymentTransfer->getOrderId());

        $unzerApiMarketplaceAuthorizeRequestTransfer->setTypeId($unzerPaymentTransfer->getPaymentResource()->getId());
        $unzerApiMarketplaceAuthorizeRequestTransfer->setCustomerId($unzerPaymentTransfer->getCustomer()->getId());
        $unzerApiMarketplaceAuthorizeRequestTransfer->setBasketId($unzerPaymentTransfer->getBasket()->getId());
        $unzerApiMarketplaceAuthorizeRequestTransfer->setReturnUrl($this->unzerConfig->getAuthorizeReturnUrl());

        return $unzerApiMarketplaceAuthorizeRequestTransfer;
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
        $unzerPaymentTransfer->setId($unzerApiMarketplaceAuthorizeResponseTransfer->getPaymentId())
            ->setAmountTotal($unzerApiMarketplaceAuthorizeResponseTransfer->getAmount() * 100)
            ->setCurrency($unzerApiMarketplaceAuthorizeResponseTransfer->getCurrency())
            ->setRedirectUrl($unzerApiMarketplaceAuthorizeResponseTransfer->getRedirectUrl())
            ->setCustomer(
                (new UnzerCustomerTransfer())->setId($unzerApiMarketplaceAuthorizeResponseTransfer->getCustomerId())
            )
            ->setBasket(
                (new UnzerBasketTransfer())->setId($unzerApiMarketplaceAuthorizeResponseTransfer->getBasketId())
            )
            ->setPaymentResource(
                (new UnzerPaymentResourceTransfer())->setId($unzerApiMarketplaceAuthorizeResponseTransfer->getTypeId())
            )
            ->setRedirectUrl($unzerApiMarketplaceAuthorizeResponseTransfer->getRedirectUrl());

        return $unzerPaymentTransfer;
    }
}
