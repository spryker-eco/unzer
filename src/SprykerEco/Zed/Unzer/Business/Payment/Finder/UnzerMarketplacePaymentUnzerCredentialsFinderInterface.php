<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Finder;

use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsFinderCriteriaTransfer;

interface UnzerMarketplacePaymentUnzerCredentialsFinderInterface
{
 /**
  * @param \Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsFinderCriteriaTransfer $unzerMarketplacePaymentCredentialsFinderCriteriaTransfer
  *
  * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
  */
    public function findMarketplacePaymentUnzerCredentials(
        UnzerMarketplacePaymentCredentialsFinderCriteriaTransfer $unzerMarketplacePaymentCredentialsFinderCriteriaTransfer
    ): UnzerCredentialsTransfer;
}
