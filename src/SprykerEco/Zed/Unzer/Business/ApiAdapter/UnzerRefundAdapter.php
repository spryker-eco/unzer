<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiMarketplaceRefundRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRefundRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerRefundTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerRefundMapperInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerRefundAdapter extends UnzerAbstractApiAdapter implements UnzerRefundAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerRefundMapperInterface
     */
    protected $unzerRefundMapper;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerRefundMapperInterface $unzerPaymentResourceMapper
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerRefundMapperInterface $unzerPaymentResourceMapper
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerRefundMapper = $unzerPaymentResourceMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerRefundTransfer $unzerRefundTransfer
     *
     * @return void
     */
    public function refundPayment(UnzerRefundTransfer $unzerRefundTransfer): void
    {
        if ($unzerRefundTransfer->getIsMarketplace()) {
            $this->performMarketplaceRefund($unzerRefundTransfer);

            return;
        }

        $this->performRegularRefund($unzerRefundTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerRefundTransfer $unzerRefundTransfer
     *
     * @return void
     */
    protected function performMarketplaceRefund(UnzerRefundTransfer $unzerRefundTransfer): void
    {
        $unzerApiMarketplaceRefundRequestTransfer = $this->unzerRefundMapper
            ->mapUnzerRefundTransferToUnzerApiMarketplaceRefundRequestTransfer(
                $unzerRefundTransfer,
                new UnzerApiMarketplaceRefundRequestTransfer()
            );

        $unzerApiRequestTransfer = new UnzerApiRequestTransfer();
        $unzerApiRequestTransfer->setMarketplaceRefundRequest($unzerApiMarketplaceRefundRequestTransfer);

        $unzerApiResponseTransfer = $this->unzerApiFacade->performMarketplaceRefundApiCall($unzerApiRequestTransfer);
        $this->checkSuccessResponse($unzerApiResponseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerRefundTransfer $unzerRefundTransfer
     *
     * @return void
     */
    protected function performRegularRefund(UnzerRefundTransfer $unzerRefundTransfer): void
    {
        $unzerApiRefundRequestTransfer = $this->unzerRefundMapper
            ->mapUnzerRefundTransferToUnzerApiRefundRequestTransfer(
                $unzerRefundTransfer,
                new UnzerApiRefundRequestTransfer()
            );

        $unzerApiRequestTransfer = new UnzerApiRequestTransfer();
        $unzerApiRequestTransfer->setRefundRequest($unzerApiRefundRequestTransfer);

        $unzerApiResponseTransfer = $this->unzerApiFacade->performRefundApiCall($unzerApiRequestTransfer);
        $this->checkSuccessResponse($unzerApiResponseTransfer);
    }
}
