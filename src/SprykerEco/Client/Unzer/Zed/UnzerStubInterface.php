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

interface UnzerStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerNotificationTransfer $notificationTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerNotificationTransfer
     */
    public function processNotification(UnzerNotificationTransfer $notificationTransfer): UnzerNotificationTransfer;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer|null
     */
    public function findUpdatedUnzerPaymentForOrderAction(OrderTransfer $orderTransfer): ?UnzerPaymentTransfer;

    /**
     * @param \Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function findMarketplacePaymentUnzerCredentials(
        UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer
    ): UnzerCredentialsTransfer;
}
