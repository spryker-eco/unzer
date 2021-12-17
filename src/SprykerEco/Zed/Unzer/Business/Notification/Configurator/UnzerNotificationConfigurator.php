<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Notification\Configurator;

use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerNotificationConfigTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerNotificationAdapterInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerNotificationConfigurator implements UnzerNotificationConfiguratorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerNotificationAdapterInterface
     */
    protected $unzerNotificationAdapter;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerNotificationAdapterInterface $unzerNotificationAdapter
     */
    public function __construct(
        UnzerConfig $unzerConfig,
        UnzerNotificationAdapterInterface $unzerNotificationAdapter
    ) {
        $this->unzerConfig = $unzerConfig;
        $this->unzerNotificationAdapter = $unzerNotificationAdapter;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer
     *
     * @return void
     */
    public function setNotificationUrl(UnzerCredentialsTransfer $unzerCredentialsTransfer): void
    {
        $unzerNotificationConfigTransfer = (new UnzerNotificationConfigTransfer())
            ->setUrl($this->unzerConfig->getWebhookRetrieveUrl())
            ->setEvent($this->unzerConfig->getWebhookEventType())
            ->setUnzerKeyPair($unzerCredentialsTransfer->getUnzerKeypairOrFail());

        $this->unzerNotificationAdapter->setNotificationUrl($unzerNotificationConfigTransfer);
    }
}
