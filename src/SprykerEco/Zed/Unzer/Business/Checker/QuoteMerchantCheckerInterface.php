<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Checker;

use Generated\Shared\Transfer\QuoteTransfer;

interface QuoteMerchantCheckerInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function hasMultipleMerchants(QuoteTransfer $quoteTransfer): bool;
}
