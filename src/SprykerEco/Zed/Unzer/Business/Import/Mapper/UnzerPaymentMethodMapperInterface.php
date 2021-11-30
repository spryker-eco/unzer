<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import\Mapper;

use ArrayObject;

interface UnzerPaymentMethodMapperInterface
{
    /**
     * @param \ArrayObject|array<\Generated\Shared\Transfer\UnzerApiPaymentTypeTransfer> $unzerApiPaymentTypeTransfers
     *
     * @return \ArrayObject
     */
    public function mapUnzerApiPaymentTypeTransfersToPaymentMethodTransfers(ArrayObject $unzerApiPaymentTypeTransfers): ArrayObject;
}
