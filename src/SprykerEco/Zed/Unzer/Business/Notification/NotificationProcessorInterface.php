<?php

namespace SprykerEco\Zed\Unzer\Business\Notification;

use Generated\Shared\Transfer\UnzerNotificationTransfer;

interface NotificationProcessorInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerNotificationTransfer $unzerNotificationTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerNotificationTransfer
     */
    public function processNotification(UnzerNotificationTransfer $unzerNotificationTransfer): UnzerNotificationTransfer;
}
