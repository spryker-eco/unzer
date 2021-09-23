<?php

namespace SprykerEco\Zed\Unzer\Dependency;

use Generated\Shared\Transfer\OrderTransfer;

interface UnzerToCalculationFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function recalculateOrder(OrderTransfer $orderTransfer);
}
