<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerCustomerTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;

interface UnzerCustomerAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     * @param UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function createCustomer(
        UnzerCustomerTransfer $unzerCustomerTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): UnzerCustomerTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     * @param UnzerKeypairTransfer $unzerKeypairTransfer

     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function updateCustomer(
        UnzerCustomerTransfer $unzerCustomerTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): UnzerCustomerTransfer;
}
