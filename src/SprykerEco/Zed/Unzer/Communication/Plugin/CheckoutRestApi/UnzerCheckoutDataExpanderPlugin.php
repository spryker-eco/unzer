<?php

namespace SprykerEco\Zed\Unzer\Communication\Plugin\CheckoutRestApi;

use Generated\Shared\Transfer\RestCheckoutDataTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Zed\CheckoutRestApiExtension\Dependency\Plugin\CheckoutDataExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface getFacade()
 */
class UnzerCheckoutDataExpanderPlugin extends AbstractPlugin implements CheckoutDataExpanderPluginInterface
{
    /**
     * Specification:
     * - Expands `RestCheckoutDataTransfer` quote with `UnzerCredentialsTransfer`.
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
                $restCheckoutDataTransfer->getQuote()
            );

        return $restCheckoutDataTransfer->setQuote($quoteTransfer);
    }
}
