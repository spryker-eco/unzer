<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import;

interface UnzerPaymentMethodImporterInterface
{
    /**
     * @return void
     */
    public function performPaymentMethodImport(): void;
}
