<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import\Filter;

use ArrayObject;

interface UnzerPaymentMethodImportFilterInterface
{
    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\PaymentMethodTransfer> $paymentMethodTransfers
     * @param \ArrayObject<int, \Generated\Shared\Transfer\PaymentMethodTransfer> $storedPaymentMethodTransfers
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\PaymentMethodTransfer>
     */
    public function filterStoredPaymentMethods(ArrayObject $paymentMethodTransfers, ArrayObject $storedPaymentMethodTransfers): ArrayObject;
}
