<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;

interface UnzerCustomerAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function createCustomer(
        UnzerCustomerTransfer $unzerCustomerTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): UnzerCustomerTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer

     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function updateCustomer(
        UnzerCustomerTransfer $unzerCustomerTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): UnzerCustomerTransfer;
}
