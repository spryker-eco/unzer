<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Dependency;

use Generated\Shared\Transfer\PaymentMethodCollectionRequestTransfer;
use Generated\Shared\Transfer\PaymentMethodCollectionResponseTransfer;
use Generated\Shared\Transfer\PaymentProviderCollectionRequestTransfer;
use Generated\Shared\Transfer\PaymentProviderCollectionResponseTransfer;
use Generated\Shared\Transfer\PaymentProviderCollectionTransfer;
use Generated\Shared\Transfer\PaymentProviderCriteriaTransfer;

interface UnzerToPaymentFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\PaymentProviderCriteriaTransfer $paymentProviderCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentProviderCollectionTransfer
     */
    public function getPaymentProviderCollection(PaymentProviderCriteriaTransfer $paymentProviderCriteriaTransfer): PaymentProviderCollectionTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaymentProviderCollectionRequestTransfer $paymentProviderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentProviderCollectionResponseTransfer
     */
    public function createPaymentProviderCollection(
        PaymentProviderCollectionRequestTransfer $paymentProviderCollectionRequestTransfer
    ): PaymentProviderCollectionResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodCollectionRequestTransfer $paymentMethodCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodCollectionResponseTransfer
     */
    public function createPaymentMethodCollection(
        PaymentMethodCollectionRequestTransfer $paymentMethodCollectionRequestTransfer
    ): PaymentMethodCollectionResponseTransfer;
}
