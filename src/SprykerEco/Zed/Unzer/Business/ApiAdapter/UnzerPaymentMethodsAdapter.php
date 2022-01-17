<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\UnzerApiGetPaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMethodsMapperInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerPaymentMethodsAdapter extends UnzerAbstractApiAdapter implements UnzerPaymentMethodsAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMethodsMapperInterface
     */
    protected $unzerGetPaymentMethodsMapper;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMethodsMapperInterface $unzerGetPaymentMethodsMapper
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerGetPaymentMethodsMapperInterface $unzerGetPaymentMethodsMapper
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerGetPaymentMethodsMapper = $unzerGetPaymentMethodsMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function getPaymentMethods(UnzerKeypairTransfer $unzerKeypairTransfer): PaymentMethodsTransfer
    {
        $unzerApiResponseTransfer = $this->performGetPaymentMethodsApiCall($unzerKeypairTransfer);
        $this->assertSuccessResponse($unzerApiResponseTransfer);

        return $this->unzerGetPaymentMethodsMapper->mapUnzerApiGetPaymentMethodsResponseTransferToPaymentMethodsTransfer(
            $unzerApiResponseTransfer->getGetPaymentMethodsResponseOrFail(),
            new PaymentMethodsTransfer(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiResponseTransfer
     */
    protected function performGetPaymentMethodsApiCall(UnzerKeypairTransfer $unzerKeypairTransfer): UnzerApiResponseTransfer
    {
        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setUnzerKeypair($unzerKeypairTransfer)
            ->setGetPaymentMethodsRequest(new UnzerApiGetPaymentMethodsRequestTransfer());

        return $this->unzerApiFacade->performGetPaymentMethodsApiCall($unzerApiRequestTransfer);
    }
}
