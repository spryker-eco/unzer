<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreSaveInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Unzer\Communication\UnzerCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\Unzer\UnzerConfig getConfig()
 */
class UnzerCheckoutPreSaveOrderPlugin extends AbstractPlugin implements CheckoutPreSaveInterface
{
    /**
     * {@inheritDoc}
     * - Requires `QuoteTransfer.payment.unzerPayment` to be set.
     * - Requires `QuoteTransfer.customer` to be set.
     * - Requires `QuoteTransfer.store` to be set.
     * - Expands `QuoteTransfer` with `UnzerPaymentTransfer`.
     * - Expands `QuoteTransfer` with `UnzerKeypairTransfer`.
     * - Expands `QuoteTransfer` with `UnzerCustomerTransfer`.
     * - Expands `QuoteTransfer` with `UnzerMetadataTransfer`.
     * - If `QuoteTransfer` contains marketplace items - expands `QuoteTransfer.items` with Unzer Participant ID.
     * - Performs Unzer Create Customer API call.
     * - Performs Unzer Update Customer API call.
     * - Performs Unzer Create Metadata API call.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function preSave(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        return $this->getFacade()->performPreSaveOrderStack($quoteTransfer);
    }
}
