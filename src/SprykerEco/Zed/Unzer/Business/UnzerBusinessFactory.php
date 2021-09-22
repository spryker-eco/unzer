<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Zed\Unzer\Business\Payment\Filter\UnzerMarketplacePaymentMethodFilter;
use SprykerEco\Zed\Unzer\Business\Payment\Filter\UnzerPaymentMethodFilterInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiInterface;
use SprykerEco\Zed\Unzer\UnzerDependencyProvider;

/**
 * @method \SprykerEco\Zed\Unzer\UnzerConfig getConfig()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface getEntityManager()
 */
class UnzerBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiInterface
     */
    public function getUnzerApiFacade(): UnzerToUnzerApiInterface
    {
        /** @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiInterface $unzerApiFacade */
        $unzerApiFacade = $this->getProvidedDependency(UnzerDependencyProvider::FACADE_UNZER_API);

        return $unzerApiFacade;
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Business\Payment\Filter\UnzerPaymentMethodFilterInterface
     */
    public function createPaymentMethodFilter(): UnzerPaymentMethodFilterInterface
    {
        return new UnzerMarketplacePaymentMethodFilter($this->getConfig());
    }
}
