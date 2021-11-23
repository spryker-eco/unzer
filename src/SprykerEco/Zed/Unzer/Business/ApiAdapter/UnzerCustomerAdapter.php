<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiCreateCustomerRequestTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiUpdateCustomerRequestTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
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
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function createCustomer(
        UnzerCustomerTransfer $unzerCustomerTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): UnzerCustomerTransfer {
        $unzerApiCreateCustomerRequestTransfer = $this->unzerCustomerMapper
            ->mapUnzerCustomerTransferToUnzerApiCreateCustomerRequestTransfer(
                $unzerCustomerTransfer,
                new UnzerApiCreateCustomerRequestTransfer(),
            );

        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setCreateCustomerRequest($unzerApiCreateCustomerRequestTransfer)
            ->setUnzerKeypair($unzerKeypairTransfer);

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
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function updateCustomer(
        UnzerCustomerTransfer $unzerCustomerTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): UnzerCustomerTransfer {
        $unzerApiUpdateCustomerRequestTransfer = $this->unzerCustomerMapper
            ->mapUnzerCustomerTransferToUnzerApiUpdateCustomerRequestTransfer(
                $unzerCustomerTransfer,
                new UnzerApiUpdateCustomerRequestTransfer(),
            );

        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setUpdateCustomerRequest($unzerApiUpdateCustomerRequestTransfer)
            ->setUnzerKeypair($unzerKeypairTransfer);

        $unzerApiResponseTransfer = $this->unzerApiFacade->performUpdateCustomerApiCall($unzerApiRequestTransfer);
        $this->assertSuccessResponse($unzerApiResponseTransfer);
        $unzerApiUpdateCustomerResponseTransfer = $unzerApiResponseTransfer->getUpdateCustomerResponseOrFail();

        return $this->unzerCustomerMapper
            ->mapUnzerApiUpdateCustomerResponseTransferToUnzerCustomerTransfer(
                $unzerApiUpdateCustomerResponseTransfer,
                $unzerCustomerTransfer,
            );
    }
}
