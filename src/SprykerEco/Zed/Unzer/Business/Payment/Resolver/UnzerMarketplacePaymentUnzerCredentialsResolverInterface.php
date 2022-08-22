<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Resolver;

use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer;

interface UnzerMarketplacePaymentUnzerCredentialsResolverInterface
{
 /**
  * @param \Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer
  *
  * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
  */
    public function findMarketplacePaymentUnzerCredentials(
        UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer
    ): UnzerCredentialsTransfer;
}
