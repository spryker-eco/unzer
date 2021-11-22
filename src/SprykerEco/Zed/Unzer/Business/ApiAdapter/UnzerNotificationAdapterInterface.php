<?php


namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerNotificationConfigTransfer;

interface UnzerNotificationAdapterInterface
{
    /**
     * @param UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer
     */
    public function setNotificationUrl(UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer): void;
}
