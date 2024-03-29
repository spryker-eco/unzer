<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\Unzer\Zed;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Client\Unzer\Dependency\Client\UnzerToZedRequestClientInterface;

class UnzerStub implements UnzerStubInterface
{
    /**
     * @var \SprykerEco\Client\Unzer\Dependency\Client\UnzerToZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * @param \SprykerEco\Client\Unzer\Dependency\Client\UnzerToZedRequestClientInterface $zedRequestClient
     */
    public function __construct(UnzerToZedRequestClientInterface $zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * @uses \SprykerEco\Zed\Unzer\Communication\Controller\GatewayController::processNotificationAction()
     *
     * @param \Generated\Shared\Transfer\UnzerNotificationTransfer $notificationTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerNotificationTransfer
     */
    public function processNotification(UnzerNotificationTransfer $notificationTransfer): UnzerNotificationTransfer
    {
        /** @var \Generated\Shared\Transfer\UnzerNotificationTransfer $unzerNotificationTransfer */
        $unzerNotificationTransfer = $this->zedRequestClient->call('/unzer/gateway/process-notification', $notificationTransfer);

        return $unzerNotificationTransfer;
    }

    /**
     * @uses \SprykerEco\Zed\Unzer\Communication\Controller\GatewayController::findUpdatedUnzerPaymentForOrderAction()
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer|null
     */
    public function findUpdatedUnzerPaymentForOrderAction(OrderTransfer $orderTransfer): ?UnzerPaymentTransfer
    {
        /** @var \Generated\Shared\Transfer\UnzerPaymentTransfer|null $unzerPaymentTransfer */
        $unzerPaymentTransfer = $this->zedRequestClient->call('/unzer/gateway/find-updated-unzer-payment-for-order', $orderTransfer);

        return $unzerPaymentTransfer;
    }

    /**
     * @uses \SprykerEco\Zed\Unzer\Communication\Controller\GatewayController::resolveMarketplacePaymentUnzerCredentialsAction()
     *
     * @param \Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function resolveMarketplacePaymentUnzerCredentials(
        UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer
    ): UnzerCredentialsTransfer {
        /** @var \Generated\Shared\Transfer\UnzerCredentialsTransfer $unzerCredentialsTransfer */
        $unzerCredentialsTransfer = $this->zedRequestClient->call('/unzer/gateway/resolve-marketplace-payment-unzer-credentials', $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer);

        return $unzerCredentialsTransfer;
    }
}
