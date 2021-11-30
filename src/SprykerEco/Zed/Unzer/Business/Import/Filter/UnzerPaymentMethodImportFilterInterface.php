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
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer|\ArrayObject $paymentMethodTransfers
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer|\ArrayObject $storedPaymentMethodTransfers
     *
     * @return \ArrayObject
     */
    public function filterStoredPaymentMethods(ArrayObject $paymentMethodTransfers, ArrayObject $storedPaymentMethodTransfers): ArrayObject;
}
