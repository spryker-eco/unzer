<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import\Filter;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodTransfer;

class UnzerPaymentMethodImportImportFilter implements UnzerPaymentMethodImportFilterInterface
{
    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer|\ArrayObject $paymentMethodTransfers
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer|\ArrayObject $storedPaymentMethodTransfers
     *
     * @return \ArrayObject
     */
    public function filterStoredPaymentMethods(ArrayObject $paymentMethodTransfers, ArrayObject $storedPaymentMethodTransfers): ArrayObject
    {
        $filteredPaymentMethodTransfers = new ArrayObject();

        foreach ($paymentMethodTransfers as $paymentMethodTransfer) {
            if (!$this->isAlreadyStored($paymentMethodTransfer, $storedPaymentMethodTransfers)) {
                $filteredPaymentMethodTransfers->append($paymentMethodTransfer);
            }
        }

        return $filteredPaymentMethodTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer|\ArrayObject $storedPaymentMethodTransfers
     *
     * @return bool
     */
    protected function isAlreadyStored(PaymentMethodTransfer $paymentMethodTransfer, ArrayObject $storedPaymentMethodTransfers): bool
    {
        foreach ($storedPaymentMethodTransfers as $storedPaymentMethodTransfer) {
            if ($paymentMethodTransfer->getPaymentMethodKey() === $storedPaymentMethodTransfer->getPaymentMethodKey()) {
                return true;
            }
        }

        return false;
    }
}
