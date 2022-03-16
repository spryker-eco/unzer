<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiSetWebhookRequestTransfer;
use Generated\Shared\Transfer\UnzerNotificationConfigTransfer;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerNotificationAdapter extends UnzerAbstractApiAdapter implements UnzerNotificationAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     */
    public function __construct(UnzerToUnzerApiFacadeInterface $unzerApiFacade)
    {
        $this->unzerApiFacade = $unzerApiFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer
     *
     * @return void
     */
    public function setNotificationUrl(UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer): void
    {
        $unzerApiSetWebhookRequestTransfer = (new UnzerApiSetWebhookRequestTransfer())
            ->setEvent($unzerNotificationConfigTransfer->getEvent())
            ->setRetrieveUrl($unzerNotificationConfigTransfer->getUrl());

        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setSetWebhookRequest($unzerApiSetWebhookRequestTransfer)
            ->setUnzerKeypair($unzerNotificationConfigTransfer->getUnzerKeyPairOrFail());

        $unzerApiResponseTransfer = $this->unzerApiFacade->performSetNotificationUrlApiCall($unzerApiRequestTransfer);
        $this->assertSuccessResponse($unzerApiResponseTransfer);
    }
}
