<?php declare(strict_types = 1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

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
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
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
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addUnzerApiFacade($container);
        $container = $this->addQuoteClient($container);
        $container = $this->addRefundFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function providePersistenceLayerDependencies(Container $container)
    {
        //TODO Provide dependencies

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
            return $container->getLocator()->unzerApi()->facade();
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
            return $container->getLocator()->quote()->client();
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
            return $container->getLocator()->sales()->facade();
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
            return $container->getLocator()->calculation()->facade();
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
            return $container->getLocator()->refund()->facade();
        });

        return $container;
    }
}
