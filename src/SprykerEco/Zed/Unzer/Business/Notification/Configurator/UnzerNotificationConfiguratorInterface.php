<?php

namespace SprykerEco\Zed\Unzer\Business\Notification\Configurator;

use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerNotificationConfigTransfer;

interface UnzerNotificationConfiguratorInterface
{
    /**
     * @param UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer
     */
    public function setNotificationUrl(UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer): void;
}
