<?php

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter;

use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiSetWebhookRequestTransfer;
use Generated\Shared\Transfer\UnzerNotificationConfigTransfer;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;

class UnzerNotificationAdapter extends UnzerAbstractApiAdapter implements UnzerNotificationAdapterInterface
{
    /**
     * @var UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    public function __construct(UnzerToUnzerApiFacadeInterface $unzerApiFacade)
    {
        $this->unzerApiFacade = $unzerApiFacade;
    }

    /**
     * @param UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerApiException
     */
    public function setNotificationUrl(UnzerNotificationConfigTransfer $unzerNotificationConfigTransfer): void
    {
        $unzerApiSetWebhookRequestTransfer = (new UnzerApiSetWebhookRequestTransfer())
            ->setEvent($unzerNotificationConfigTransfer->getEvent())
            ->setRetrieveUrl($unzerNotificationConfigTransfer->getUrl());

        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())
            ->setSetWebhookRequest($unzerApiSetWebhookRequestTransfer)
            ->setUnzerKeypair($unzerNotificationConfigTransfer->getUnzerKeyPairOrFail());

        $unzerApiResponseTransfer = $this->unzerApiFacade->performCreateCustomerApiCall($unzerApiRequestTransfer);
        $this->assertSuccessResponse($unzerApiResponseTransfer);
    }
}
