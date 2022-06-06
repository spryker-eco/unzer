<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;

interface UnzerPaymentResourceAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentResourceTransfer
     */
    public function createPaymentResource(
        UnzerPaymentResourceTransfer $unzerPaymentResourceTransfer,
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): UnzerPaymentResourceTransfer;
}
