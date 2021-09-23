<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiCreateCustomerRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreateCustomerResponseTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;

class UnzerCustomerMapper implements UnzerCustomerMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     * @param \Generated\Shared\Transfer\UnzerApiCreateCustomerRequestTransfer $unzerApiCreateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiCreateCustomerRequestTransfer
     */
    public function mapUnzerCustomerTransferToUnzerApiCreateCustomerRequestTransfer(
        UnzerCustomerTransfer $unzerCustomerTransfer,
        UnzerApiCreateCustomerRequestTransfer $unzerApiCreateCustomerRequestTransfer
    ): UnzerApiCreateCustomerRequestTransfer {
        $unzerApiCreateCustomerRequestTransfer->fromArray($unzerCustomerTransfer->toArray(), true);
        $unzerApiCreateCustomerRequestTransfer->setCustomerId($unzerCustomerTransfer->getId());

        return $unzerApiCreateCustomerRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiCreateCustomerResponseTransfer $createCustomerResponse
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function mapUnzerApiCreateCustomerResponseTransferToUnzerCustomerTransfer(
        UnzerApiCreateCustomerResponseTransfer $createCustomerResponse,
        UnzerCustomerTransfer $unzerCustomerTransfer
    ): UnzerCustomerTransfer {
        $unzerCustomerTransfer->fromArray($createCustomerResponse->toArray());

        return $unzerCustomerTransfer;
    }
}
