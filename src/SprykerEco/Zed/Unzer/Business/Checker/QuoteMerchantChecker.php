<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Checker;

use Generated\Shared\Transfer\QuoteTransfer;

class QuoteMerchantChecker implements QuoteMerchantCheckerInterface
{
    /**
     * @var string
     */
    protected const MAIN_SELLER_REFERENCE = 'main';

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function hasMultipleMerchants(QuoteTransfer $quoteTransfer): bool
    {
        $merchantReferences = [];

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $merchantReference = $itemTransfer->getMerchantReference();

            if (!$merchantReference && !in_array(static::MAIN_SELLER_REFERENCE, $merchantReferences, true)) {
                $merchantReferences[] = static::MAIN_SELLER_REFERENCE;

                continue;
            }

            if ($merchantReference && !in_array($merchantReference, $merchantReferences, true)) {
                $merchantReferences[] = $merchantReference;
            }
        }

        return count($merchantReferences) > 1;
    }
}
