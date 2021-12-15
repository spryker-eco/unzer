<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import\Filter;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodTransfer;

class UnzerPaymentMethodImportFilter implements UnzerPaymentMethodImportFilterInterface
{
    /**
     * @param \ArrayObject<\Generated\Shared\Transfer\PaymentMethodTransfer> $paymentMethodTransfers
     * @param \ArrayObject<\Generated\Shared\Transfer\PaymentMethodTransfer> $storedPaymentMethodTransfers
     *
     * @return \ArrayObject<\Generated\Shared\Transfer\PaymentMethodTransfer>
     */
    public function filterStoredPaymentMethods(
        ArrayObject $paymentMethodTransfers,
        ArrayObject $storedPaymentMethodTransfers
    ): ArrayObject {
        $filteredPaymentMethodTransfers = new ArrayObject();
        $storedPaymentMethodKeys = $this->getStoredPaymentMethodKeys($storedPaymentMethodTransfers);

        foreach ($paymentMethodTransfers as $paymentMethodTransfer) {
            if (!in_array($paymentMethodTransfer->getPaymentMethodKey(), $storedPaymentMethodKeys)) {
                $filteredPaymentMethodTransfers->append($paymentMethodTransfer);
            }
        }

        return $filteredPaymentMethodTransfers;
    }

    /**
     * @param \ArrayObject $storedPaymentMethodTransfers
     *
     * @return array<int, string>
     */
    protected function getStoredPaymentMethodKeys(ArrayObject $storedPaymentMethodTransfers): array
    {
        return array_map(function (PaymentMethodTransfer $paymentMethodTransfer) {
            return $paymentMethodTransfer->getPaymentMethodKey();
        }, (array)$storedPaymentMethodTransfers);
    }
}
