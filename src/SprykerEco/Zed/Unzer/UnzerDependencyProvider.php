<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\Unzer\Dependency\UnzerToCalculationFacadeBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToLocaleFacadeBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToPaymentFacadeBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToQuoteClientBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToRefundFacadeBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUtilTextServiceBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToVaultFacadeBridge;

/**
 * @method \SprykerEco\Zed\Unzer\UnzerConfig getConfig()
 */
class UnzerDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_UNZER_API = 'FACADE_UNZER_API';

    /**
     * @var string
     */
    public const FACADE_SALES = 'FACADE_SALES';

    /**
     * @var string
     */
    public const FACADE_CALCULATION = 'FACADE_CALCULATION';

    /**
     * @var string
     */
    public const FACADE_REFUND = 'FACADE_REFUND';

    /**
     * @var string
     */
    public const CLIENT_QUOTE = 'CLIENT_QUOTE';

    /**
     * @var string
     */
    public const FACADE_PAYMENT = 'FACADE_PAYMENT';

    /**
     * @var string
     */
    public const FACADE_LOCALE = 'FACADE_LOCALE';

    /**
     * @var string
     */
    public const FACADE_VAULT = 'FACADE_VAULT';

    /**
     * @var string
     */
    public const SERVICE_UTIL_TEXT = 'SERVICE_UTIL_TEXT';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);
        $container = $this->addSalesFacade($container);
        $container = $this->addCalculationFacade($container);
        $container = $this->addRefundFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addUnzerApiFacade($container);
        $container = $this->addQuoteClient($container);
        $container = $this->addRefundFacade($container);
        $container = $this->addPaymentFacade($container);
        $container = $this->addLocaleFacade($container);
        $container = $this->addVaultFacade($container);
        $container = $this->addUtilTextService($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUnzerApiFacade(Container $container): Container
    {
        $container->set(static::FACADE_UNZER_API, function (Container $container) {
            return new UnzerToUnzerApiFacadeBridge($container->getLocator()->unzerApi()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addQuoteClient(Container $container): Container
    {
        $container->set(static::CLIENT_QUOTE, function (Container $container) {
            return new UnzerToQuoteClientBridge($container->getLocator()->quote()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSalesFacade(Container $container): Container
    {
        $container->set(static::FACADE_SALES, function (Container $container) {
            return new UnzerToSalesFacadeBridge($container->getLocator()->sales()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCalculationFacade(Container $container): Container
    {
        $container->set(static::FACADE_CALCULATION, function (Container $container) {
            return new UnzerToCalculationFacadeBridge($container->getLocator()->calculation()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addRefundFacade(Container $container): Container
    {
        $container->set(static::FACADE_REFUND, function (Container $container) {
            return new UnzerToRefundFacadeBridge($container->getLocator()->refund()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addLocaleFacade(Container $container): Container
    {
        $container->set(static::FACADE_LOCALE, function (Container $container) {
            return new UnzerToLocaleFacadeBridge($container->getLocator()->locale()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addVaultFacade(Container $container): Container
    {
        $container->set(static::FACADE_VAULT, function (Container $container) {
            return new UnzerToVaultFacadeBridge($container->getLocator()->vault()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilTextService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_TEXT, function (Container $container) {
            return new UnzerToUtilTextServiceBridge($container->getLocator()->utilText()->service());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPaymentFacade(Container $container): Container
    {
        $container->set(static::FACADE_PAYMENT, function (Container $container) {
            return new UnzerToPaymentFacadeBridge($container->getLocator()->payment()->facade());
        });

        return $container;
    }
}
