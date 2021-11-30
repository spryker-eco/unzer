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
     * @param \ArrayObject|array<\Generated\Shared\Transfer\PaymentMethodTransfer> $newPaymentMethodTransfers
     * @param \ArrayObject|array<\Generated\Shared\Transfer\PaymentMethodTransfer> $existingPaymentMethodTransfers
     *
     * @return \ArrayObject
     */
    public function filterStoredPaymentMethods(ArrayObject $newPaymentMethodTransfers, ArrayObject $existingPaymentMethodTransfers): ArrayObject;
}
