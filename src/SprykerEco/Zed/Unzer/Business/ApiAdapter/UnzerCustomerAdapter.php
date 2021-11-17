<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiCreateCustomerRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerCustomerMapperInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerCustomerAdapter extends UnzerAbstractApiAdapter implements UnzerCustomerAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerCustomerMapperInterface
     */
    protected $unzerCustomerMapper;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerCustomerMapperInterface $unzerCustomerMapper
     */
    public function __construct(
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerCustomerMapperInterface $unzerCustomerMapper
    ) {
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerCustomerMapper = $unzerCustomerMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function createCustomer(UnzerCustomerTransfer $unzerCustomerTransfer): UnzerCustomerTransfer
    {
        $unzerApiCreateCustomerRequestTransfer = $this->unzerCustomerMapper
            ->mapUnzerCustomerTransferToUnzerApiCreateCustomerRequestTransfer(
                $unzerCustomerTransfer,
                new UnzerApiCreateCustomerRequestTransfer(),
            );

        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setCreateCustomerRequest($unzerApiCreateCustomerRequestTransfer);

        $unzerApiResponseTransfer = $this->unzerApiFacade->performCreateCustomerApiCall($unzerApiRequestTransfer);
        $this->assertSuccessResponse($unzerApiResponseTransfer);
        $unzerApiCreateCustomerResponseTransfer = $unzerApiResponseTransfer->getCreateCustomerResponseOrFail();

        return $this->unzerCustomerMapper
            ->mapUnzerApiCreateCustomerResponseTransferToUnzerCustomerTransfer(
                $unzerApiCreateCustomerResponseTransfer,
                $unzerCustomerTransfer,
            );
    }

    /**
     * @param UnzerCustomerTransfer $unzerCustomerTransfer
     *
     * @return UnzerCustomerTransfer
     */
    public function updateCustomer(UnzerCustomerTransfer $unzerCustomerTransfer): UnzerCustomerTransfer
    {
        $unzerApiUpdateCustomerRequestTransfer = $this->unzerCustomerMapper
            ->mapUnzerCustomerTransferToUnzerApiCreateCustomerRequestTransfer(
                $unzerCustomerTransfer,
                new UnzerApiUpdateCustomerRequestTransfer(),
            );

        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setUpdateCustomerRequest($unzerApiUpdateCustomerRequestTransfer);

        $unzerApiResponseTransfer = $this->unzerApiFacade->performCreateCustomerApiCall($unzerApiRequestTransfer);
        $this->assertSuccessResponse($unzerApiResponseTransfer);
        $unzerApiCreateCustomerResponseTransfer = $unzerApiResponseTransfer->getCreateCustomerResponseOrFail();

        return $this->unzerCustomerMapper
            ->mapUnzerApiCreateCustomerResponseTransferToUnzerCustomerTransfer(
                $unzerApiCreateCustomerResponseTransfer,
                $unzerCustomerTransfer,
            );
    }
}
