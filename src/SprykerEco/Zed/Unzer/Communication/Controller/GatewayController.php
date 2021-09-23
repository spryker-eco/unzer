<?php

namespace SprykerEco\Zed\Unzer\Communication\Controller;

use Generated\Shared\Transfer\UnzerNotificationTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \SprykerEco\Zed\Unzer\Business\UnzerFacade getFacade()
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
}
