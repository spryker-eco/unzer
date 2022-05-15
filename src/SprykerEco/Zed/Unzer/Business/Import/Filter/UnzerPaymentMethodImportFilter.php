<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import\Filter;

use ArrayObject;

class UnzerPaymentMethodImportFilter implements UnzerPaymentMethodImportFilterInterface
{
    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\PaymentMethodTransfer> $paymentMethodTransfers
     * @param \ArrayObject<int, \Generated\Shared\Transfer\PaymentMethodTransfer> $storedPaymentMethodTransfers
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\PaymentMethodTransfer>
     */
    public function filterOutStoredPaymentMethods(
        ArrayObject $paymentMethodTransfers,
        ArrayObject $storedPaymentMethodTransfers
    ): ArrayObject {
        $filteredPaymentMethodTransfers = new ArrayObject();
        $storedPaymentMethodKeys = $this->getStoredPaymentMethodKeys($storedPaymentMethodTransfers);

        foreach ($paymentMethodTransfers as $paymentMethodTransfer) {
            if (!in_array($paymentMethodTransfer->getPaymentMethodKey(), $storedPaymentMethodKeys, true)) {
                $filteredPaymentMethodTransfers->append($paymentMethodTransfer);
            }
        }

        return $filteredPaymentMethodTransfers;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\PaymentMethodTransfer> $storedPaymentMethodTransfers
     *
     * @return array<int, string>
     */
    protected function getStoredPaymentMethodKeys(ArrayObject $storedPaymentMethodTransfers): array
    {
        $storedPaymentMethodKeys = [];
        foreach ($storedPaymentMethodTransfers as $paymentMethodTransfer) {
            if ($paymentMethodTransfer->getPaymentMethodKey() !== null) {
                $storedPaymentMethodKeys[] = $paymentMethodTransfer->getPaymentMethodKey();
            }
        }

        return $storedPaymentMethodKeys;
    }
}
