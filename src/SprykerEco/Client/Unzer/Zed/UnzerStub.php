<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\Unzer\Zed;

use Generated\Shared\Transfer\UnzerNotificationTransfer;
use SprykerEco\Client\Unzer\Dependency\Client\UnzerToZedRequestClientInterface;

class UnzerStub implements UnzerStubInterface
{
    /**
     * @var \SprykerEco\Client\Unzer\Dependency\Client\UnzerToZedRequestClientInterface
     */
    protected $zedStubClient;

    /**
     * @param \SprykerEco\Client\Unzer\Dependency\Client\UnzerToZedRequestClientInterface $zedStubClient
     */
    public function __construct(UnzerToZedRequestClientInterface $zedStubClient)
    {
        $this->zedStubClient = $zedStubClient;
    }

    protected const ZED_PROCESS_NOTIFICATION = '/unzer/gateway/process-notification';

    /**
     * @param \Generated\Shared\Transfer\UnzerNotificationTransfer $notificationTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerNotificationTransfer
     */
    public function processNotification(UnzerNotificationTransfer $notificationTransfer): UnzerNotificationTransfer
    {
        /** @var \Generated\Shared\Transfer\UnzerNotificationTransfer $notificationTransfer */
        $notificationTransfer = $this->zedStubClient->call(static::ZED_PROCESS_NOTIFICATION, $notificationTransfer);

        return $notificationTransfer;
    }
}
