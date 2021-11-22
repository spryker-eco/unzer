<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Notification\Configurator;

use Generated\Shared\Transfer\UnzerNotificationConfigTransfer;

interface UnzerNotificationConfiguratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer
     *
     * @return void
     */
    public function setNotificationUrl(UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer): void;
}
