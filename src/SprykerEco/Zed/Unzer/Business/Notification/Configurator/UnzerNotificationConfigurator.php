<?php

namespace SprykerEco\Zed\Unzer\Business\Notification\Configurator;

use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiSetWebhookRequestTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use Generated\Shared\Transfer\UnzerNotificationConfigTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerNotificationAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Payment\KeypairResolver\UnzerKeypairResolverInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerNotificationConfigurator implements UnzerNotificationConfiguratorInterface
{
    /**
     * @var UnzerKeypairResolverInterface
     */
    protected $unzerKeypairResolver;

    /**
     * @var UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var UnzerNotificationAdapterInterface
     */
    protected $unzerNotificationAdapter;

    /**
     * @param UnzerKeypairResolverInterface $unzerKeypairResolver
     * @param UnzerConfig $unzerConfig
     * @param UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param UnzerNotificationAdapterInterface $unzerNotificationAdapter
     */
    public function __construct(
        UnzerKeypairResolverInterface $unzerKeypairResolver,
        UnzerConfig $unzerConfig,
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerNotificationAdapterInterface $unzerNotificationAdapter
    )
    {
        $this->unzerKeypairResolver = $unzerKeypairResolver;
        $this->unzerConfig = $unzerConfig;
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerNotificationAdapter = $unzerNotificationAdapter;
    }

    /**
     * @param UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer
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
