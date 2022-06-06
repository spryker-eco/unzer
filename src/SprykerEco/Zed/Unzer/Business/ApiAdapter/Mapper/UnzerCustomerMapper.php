<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\UnzerApiCreateCustomerRequestTransfer;
use Generated\Shared\Transfer\UnzerApiCreateCustomerResponseTransfer;
use Generated\Shared\Transfer\UnzerApiUpdateCustomerRequestTransfer;
use Generated\Shared\Transfer\UnzerApiUpdateCustomerResponseTransfer;
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
        return $unzerApiCreateCustomerRequestTransfer
            ->fromArray($unzerCustomerTransfer->toArray(), true)
            ->setCustomerId($unzerCustomerTransfer->getId());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     * @param \Generated\Shared\Transfer\UnzerApiUpdateCustomerRequestTransfer $unzerApiUpdateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiUpdateCustomerRequestTransfer
     */
    public function mapUnzerCustomerTransferToUnzerApiUpdateCustomerRequestTransfer(
        UnzerCustomerTransfer $unzerCustomerTransfer,
        UnzerApiUpdateCustomerRequestTransfer $unzerApiUpdateCustomerRequestTransfer
    ): UnzerApiUpdateCustomerRequestTransfer {
        return $unzerApiUpdateCustomerRequestTransfer->fromArray($unzerCustomerTransfer->toArray(), true);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiCreateCustomerResponseTransfer $unzerApiCreateCustomerResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function mapUnzerApiCreateCustomerResponseTransferToUnzerCustomerTransfer(
        UnzerApiCreateCustomerResponseTransfer $unzerApiCreateCustomerResponseTransfer,
        UnzerCustomerTransfer $unzerCustomerTransfer
    ): UnzerCustomerTransfer {
        return $unzerCustomerTransfer->fromArray($unzerApiCreateCustomerResponseTransfer->toArray(), true);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiUpdateCustomerResponseTransfer $unzerApiUpdateCustomerResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function mapUnzerApiUpdateCustomerResponseTransferToUnzerCustomerTransfer(
        UnzerApiUpdateCustomerResponseTransfer $unzerApiUpdateCustomerResponseTransfer,
        UnzerCustomerTransfer $unzerCustomerTransfer
    ): UnzerCustomerTransfer {
        return $unzerCustomerTransfer->fromArray($unzerApiUpdateCustomerResponseTransfer->toArray(), true);
    }
}
