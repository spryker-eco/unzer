<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiCreateCustomerRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerCustomerMapperInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerCustomerAdapter extends UnzerAbstractApiAdapter implements UnzerCustomerAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerCustomerMapperInterface
     */
    protected $unzerCustomerMapper;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerCustomerMapperInterface $unzerCustomerMapper
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerConfig $unzerConfig,
        UnzerCustomerMapperInterface $unzerCustomerMapper
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerConfig = $unzerConfig;
        $this->unzerCustomerMapper = $unzerCustomerMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function createCustomer(UnzerCustomerTransfer $unzerCustomerTransfer): UnzerCustomerTransfer
    {
        $createCustomerRequest = $this
            ->unzerCustomerMapper
            ->mapUnzerCustomerTransferToUnzerApiCreateCustomerRequestTransfer(
                $unzerCustomerTransfer,
                new UnzerApiCreateCustomerRequestTransfer()
            );

        $unzerApiRequest = new UnzerApiRequestTransfer();
        $unzerApiRequest->setCreateCustomerRequest($createCustomerRequest);

        $unzerApiResponse = $this->unzerApiFacade->performCreateCustomerApiCall($unzerApiRequest);
        $this->checkSuccessResponse($unzerApiResponse);
        $createCustomerResponse = $unzerApiResponse->getCreateCustomerResponseOrFail();

        $unzerCustomerTransfer = $this->
        unzerCustomerMapper
            ->mapUnzerApiCreateCustomerResponseTransferToUnzerCustomerTransfer(
                $createCustomerResponse,
                $unzerCustomerTransfer
            );

        return $unzerCustomerTransfer;
    }
}
