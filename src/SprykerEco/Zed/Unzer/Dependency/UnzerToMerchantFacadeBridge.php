<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Dependency;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Merchant\Business\MerchantFacade;

class UnzerToMerchantFacadeBridge implements UnzerToMerchantFacadeInterface
{
    /**
     * @var \Spryker\Zed\Merchant\Business\MerchantFacade
     */
    protected $merchantFacade;

    /**
     * @param \Spryker\Zed\Merchant\Business\MerchantFacade $merchantFacade
     */
    public function __construct(MerchantFacade $merchantFacade)
    {
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer|null
     */
    public function findOne(MerchantCriteriaTransfer $merchantCriteriaTransfer): ?MerchantTransfer
    {
        return $this->merchantFacade->findOne($merchantCriteriaTransfer);
    }
}
