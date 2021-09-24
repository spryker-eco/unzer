<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Oms\Command;

use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;

abstract class AbstractOmsCommand
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return string
     */
    protected function getPaymentMethodName(OrderTransfer $orderTransfer): string
    {
        foreach ($orderTransfer->getPayments() as $paymentTransfer) {
            if ($paymentTransfer->getPaymentProviderOrFail() === UnzerConfig::PROVIDER_NAME) {
                return $paymentTransfer->getPaymentMethodOrFail();
            }
        }

        throw new UnzerException('Unzer payment method not found!');
    }
}
