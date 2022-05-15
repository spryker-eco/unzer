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
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerPaymentMethodsAdapter implements UnzerPaymentMethodsAdapterInterface
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
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface
     */
    protected $unzerApiAdapterResponseValidator;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerGetPaymentMethodsMapperInterface $unzerGetPaymentMethodsMapper
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Validator\UnzerApiAdapterResponseValidatorInterface $unzerApiAdapterResponseValidator
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerGetPaymentMethodsMapperInterface $unzerGetPaymentMethodsMapper,
        UnzerApiAdapterResponseValidatorInterface $unzerApiAdapterResponseValidator
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerGetPaymentMethodsMapper = $unzerGetPaymentMethodsMapper;
        $this->unzerApiAdapterResponseValidator = $unzerApiAdapterResponseValidator;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function getPaymentMethods(UnzerKeypairTransfer $unzerKeypairTransfer): PaymentMethodsTransfer
    {
        $unzerApiResponseTransfer = $this->performGetPaymentMethodsApiCall($unzerKeypairTransfer);
        $this->unzerApiAdapterResponseValidator->assertSuccessResponse($unzerApiResponseTransfer);

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
