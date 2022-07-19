<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication\Plugin\CheckoutRestApi;

use Generated\Shared\Transfer\RestCheckoutDataTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Zed\CheckoutRestApiExtension\Dependency\Plugin\CheckoutDataExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Unzer\UnzerConfig getConfig()
 * @method \SprykerEco\Zed\Unzer\Communication\UnzerCommunicationFactory getFactory()
 */
class UnzerCheckoutDataExpanderPlugin extends AbstractPlugin implements CheckoutDataExpanderPluginInterface
{
    /**
     * {@inheritDoc}
     * - Requires `RestCheckoutDataTransfer.quote.store.name` transfer property to be set.
     * - Expands `RestCheckoutDataTransfer.quote` transfer property with `UnzerCredentialsTransfer` according to added items.
     * - Does nothing if quote has no items.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\RestCheckoutDataTransfer $restCheckoutDataTransfer
     * @param \Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestCheckoutDataTransfer
     */
    public function expandCheckoutData(
        RestCheckoutDataTransfer $restCheckoutDataTransfer,
        RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer
    ): RestCheckoutDataTransfer {
        $quoteTransfer = $this->getFacade()
            ->expandQuoteWithUnzerCredentials(
                $restCheckoutDataTransfer->getQuoteOrFail(),
            );

        return $restCheckoutDataTransfer->setQuote($quoteTransfer);
    }
}
