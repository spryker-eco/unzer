<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication\Controller;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsFinderCriteriaTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\Unzer\Communication\UnzerCommunicationFactory getFactory()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\UnzerNotificationTransfer $notificationTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerNotificationTransfer
     */
    public function processNotificationAction(UnzerNotificationTransfer $notificationTransfer): UnzerNotificationTransfer
    {
        return $this->getFacade()->processNotification($notificationTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer|null
     */
    public function findUpdatedUnzerPaymentForOrderAction(OrderTransfer $orderTransfer): ?UnzerPaymentTransfer
    {
        return $this->getFacade()->findUpdatedUnzerPaymentForOrder($orderTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function findMarketplacePaymentUnzerCredentialsAction(
        UnzerMarketplacePaymentCredentialsFinderCriteriaTransfer $unzerMarketplacePaymentCredentialsFinderCriteriaTransfer
    ): UnzerCredentialsTransfer {
        return $this->getFacade()->findMarketplacePaymentUnzerCredentials($unzerMarketplacePaymentCredentialsFinderCriteriaTransfer);
    }
}
