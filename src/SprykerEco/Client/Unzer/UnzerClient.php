<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\Unzer;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \SprykerEco\Client\Unzer\UnzerFactory getFactory()
 */
class UnzerClient extends AbstractClient implements UnzerClientInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerNotificationTransfer $unzerNotificationTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerNotificationTransfer
     */
    public function processNotification(UnzerNotificationTransfer $unzerNotificationTransfer): UnzerNotificationTransfer
    {
        return $this->getFactory()->createZedStub()->processNotification($unzerNotificationTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer|null
     */
    public function findUpdatedUnzerPaymentForOrderAction(OrderTransfer $orderTransfer): ?UnzerPaymentTransfer
    {
        return $this->getFactory()->createZedStub()->findUpdatedUnzerPaymentForOrderAction($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function resolveMarketplacePaymentUnzerCredentials(
        UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer
    ): UnzerCredentialsTransfer {
        return $this->getFactory()
            ->createZedStub()
            ->resolveMarketplacePaymentUnzerCredentials($unzerMarketplacePaymentCredentialsResolverCriteriaTransfer);
    }
}
