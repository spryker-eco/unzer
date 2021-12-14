<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import\Filter;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodsTransfer;

interface UnzerPaymentMethodImportFilterInterface
{
    /**
     * @param \ArrayObject<\Generated\Shared\Transfer\PaymentMethodTransfer> $paymentMethodTransfers
     * @param \ArrayObject<\Generated\Shared\Transfer\PaymentMethodTransfer> $storedPaymentMethodTransfers
     *
     * @return \ArrayObject<\Generated\Shared\Transfer\PaymentMethodTransfer>
     */
    public function filterStoredPaymentMethods(ArrayObject $paymentMethodTransfers, ArrayObject $storedPaymentMethodTransfers): ArrayObject;
}
