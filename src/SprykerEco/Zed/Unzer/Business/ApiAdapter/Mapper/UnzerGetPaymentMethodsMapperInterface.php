<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\UnzerApiGetPaymentMethodsResponseTransfer;

interface UnzerGetPaymentMethodsMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerApiGetPaymentMethodsResponseTransfer $unzerApiGetPaymentMethodsResponseTransfer
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function mapUnzerApiGetPaymentMethodsResponseTransferToPaymentMethodsTransfer(
        UnzerApiGetPaymentMethodsResponseTransfer $unzerApiGetPaymentMethodsResponseTransfer,
        PaymentMethodsTransfer $paymentMethodsTransfer
    ): PaymentMethodsTransfer;
}
