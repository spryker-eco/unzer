<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication\Plugin\Cart;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\CartExtension\Dependency\Plugin\CartOperationPostSavePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \SprykerEco\Zed\Unzer\UnzerConfig getConfig()
 * @method \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Unzer\Communication\UnzerCommunicationFactory getFactory()
 */
class UnzerCredentialsCartOperationPostSavePlugin extends AbstractPlugin implements CartOperationPostSavePluginInterface
{
    /**
     * {@inheritDoc}
     * - Requires `QuoteTransfer.store.name` transfer property to be set.
     * - Expands `QuoteTransfer` with `UnzerCredentialsTransfer` according to added items.
     * - Does nothing if quote has no items.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function postSave(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        return $this->getFacade()->expandQuoteWithUnzerCredentials($quoteTransfer);
    }
}
