<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\CheckoutExtension\Dependency\Plugin\CheckoutPostSaveInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Unzer\Communication\UnzerCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\Unzer\UnzerConfig getConfig()
 */
class UnzerCheckoutPostSavePlugin extends AbstractPlugin implements CheckoutPostSaveInterface
{
    /**
     * {@inheritDoc}
     * - Requires `QuoteTransfer.payment.unzerPayment.idSalesOrder` to be set.
     * - Requires `QuoteTransfer.payment.paymentSelection` to be set.
     * - Requires `QuoteTransfer.currency.code` to be set.
     * - Requires `QuoteTransfer.totals.grandTotal` to be set.
     * - Expands `QuoteTransfer` with `UnzerBasketTransfer`.
     * - Expands `QuoteTransfer` with `UnzerPaymentResourceTransfer`.
     * - Performs Unzer Create Basket API call.
     * - Performs Unzer Create payment resource API call.
     * - Performs Unzer Authorize or Change API call depending on payment type.
     * - Saves payment detailed info to Persistence.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function executeHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): void
    {
        $this->getFacade()->executePostSaveHook($quoteTransfer, $checkoutResponseTransfer);
    }
}
