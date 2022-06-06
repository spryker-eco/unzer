<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiMarketplaceRefundRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRefundRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerRefundTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerRefundMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerRefundAdapter implements UnzerRefundAdapterInterface
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
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface
     */
    protected $unzerApiAdapterResponseValidator;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerRefundMapperInterface $unzerPaymentResourceMapper
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface $unzerApiAdapterResponseValidator
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerRefundMapperInterface $unzerPaymentResourceMapper,
        UnzerApiAdapterResponseValidatorInterface $unzerApiAdapterResponseValidator
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerRefundMapper = $unzerPaymentResourceMapper;
        $this->unzerApiAdapterResponseValidator = $unzerApiAdapterResponseValidator;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerRefundTransfer $unzerRefundTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return void
     */
    public function refundPayment(
        UnzerRefundTransfer $unzerRefundTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): void {
        if ($unzerRefundTransfer->getIsMarketplace()) {
            $this->performMarketplaceRefund($unzerRefundTransfer, $unzerKeypairTransfer);

            return;
        }

        $this->performRegularRefund($unzerRefundTransfer, $unzerKeypairTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerRefundTransfer $unzerRefundTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return void
     */
    protected function performMarketplaceRefund(
        UnzerRefundTransfer $unzerRefundTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): void {
        $unzerApiMarketplaceRefundRequestTransfer = $this->unzerRefundMapper
            ->mapUnzerRefundTransferToUnzerApiMarketplaceRefundRequestTransfer(
                $unzerRefundTransfer,
                new UnzerApiMarketplaceRefundRequestTransfer(),
            );

        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setMarketplaceRefundRequest($unzerApiMarketplaceRefundRequestTransfer)
            ->setUnzerKeypair($unzerKeypairTransfer);

        $unzerApiResponseTransfer = $this->unzerApiFacade->performMarketplaceRefundApiCall($unzerApiRequestTransfer);
        $this->unzerApiAdapterResponseValidator->assertSuccessResponse($unzerApiResponseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerRefundTransfer $unzerRefundTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return void
     */
    protected function performRegularRefund(
        UnzerRefundTransfer $unzerRefundTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): void {
        $unzerApiRefundRequestTransfer = $this->unzerRefundMapper
            ->mapUnzerRefundTransferToUnzerApiRefundRequestTransfer(
                $unzerRefundTransfer,
                new UnzerApiRefundRequestTransfer(),
            );

        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setRefundRequest($unzerApiRefundRequestTransfer)
            ->setUnzerKeypair($unzerKeypairTransfer);

        $unzerApiResponseTransfer = $this->unzerApiFacade->performRefundApiCall($unzerApiRequestTransfer);
        $this->unzerApiAdapterResponseValidator->assertSuccessResponse($unzerApiResponseTransfer);
    }
}
