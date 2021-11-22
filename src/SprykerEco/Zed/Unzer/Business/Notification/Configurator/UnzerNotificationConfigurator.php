<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Notification\Configurator;

use Generated\Shared\Transfer\UnzerNotificationConfigTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerNotificationAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Payment\KeypairResolver\UnzerKeypairResolverInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerNotificationConfigurator implements UnzerNotificationConfiguratorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\KeypairResolver\UnzerKeypairResolverInterface
     */
    protected $unzerKeypairResolver;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerNotificationAdapterInterface
     */
    protected $unzerNotificationAdapter;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Payment\KeypairResolver\UnzerKeypairResolverInterface $unzerKeypairResolver
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerNotificationAdapterInterface $unzerNotificationAdapter
     */
    public function __construct(
        UnzerKeypairResolverInterface $unzerKeypairResolver,
        UnzerConfig $unzerConfig,
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerNotificationAdapterInterface $unzerNotificationAdapter
    ) {
        $this->unzerKeypairResolver = $unzerKeypairResolver;
        $this->unzerConfig = $unzerConfig;
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerNotificationAdapter = $unzerNotificationAdapter;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer
     *
     * @return void
     */
    public function setNotificationUrl(UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer): void
    {
        $unzerKeypairTransfer = $unzerNotificationConfigTransfer->getUnzerKeyPair();
        if ($unzerKeypairTransfer === null) {
            $unzerKeyPairId = $this->unzerConfig->getUnzerPrimaryKeypairId();
            $unzerKeypairTransfer = $this->unzerKeypairResolver->getUnzerKeypairByKeypairId($unzerKeyPairId);
            $unzerNotificationConfigTransfer->setUnzerKeyPair($unzerKeypairTransfer);
        }

        $this->unzerNotificationAdapter->setNotificationUrl($unzerNotificationConfigTransfer);
    }
}
