<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import\Adapter;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodCollectionResponseTransfer;
use Generated\Shared\Transfer\PaymentProviderCollectionResponseTransfer;
use Generated\Shared\Transfer\PaymentProviderTransfer;

interface PaymentImportAdapterInterface
{
    /**
     * @param string $paymentProviderName
     *
     * @return \Generated\Shared\Transfer\PaymentProviderTransfer|null
     */
    public function findPaymentProvider(string $paymentProviderName): ?PaymentProviderTransfer;

    /**
     * @param string $paymentProviderName
     * @param \ArrayObject<int, \Generated\Shared\Transfer\PaymentMethodTransfer> $paymentMethodTransfers
     *
     * @return \Generated\Shared\Transfer\PaymentProviderCollectionResponseTransfer
     */
    public function createPaymentProvider(
        string $paymentProviderName,
        ArrayObject $paymentMethodTransfers
    ): PaymentProviderCollectionResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaymentProviderTransfer $paymentProviderTransfer
     * @param \ArrayObject<int, \Generated\Shared\Transfer\PaymentMethodTransfer> $paymentMethodTransfers
     *
     * @return \Generated\Shared\Transfer\PaymentMethodCollectionResponseTransfer
     */
    public function createPaymentMethods(
        PaymentProviderTransfer $paymentProviderTransfer,
        ArrayObject $paymentMethodTransfers
    ): PaymentMethodCollectionResponseTransfer;
}
