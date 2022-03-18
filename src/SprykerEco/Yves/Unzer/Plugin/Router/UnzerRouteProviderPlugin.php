<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Plugin\Router;

use Spryker\Yves\Router\Plugin\RouteProvider\AbstractRouteProviderPlugin;
use Spryker\Yves\Router\Route\RouteCollection;

class UnzerRouteProviderPlugin extends AbstractRouteProviderPlugin
{
    /**
     * @var string
     */
    public const ROUTE_NAME_UNZER_PAYMENT_RESULT = 'unzer-payment-result';

    /**
     * @var string
     */
    public const ROUTE_NAME_UNZER_NOTIFICATION = 'unzer-notification';

    /**
     * Specification:
     * - Adds Routes to the RouteCollection.
     *
     * @api
     *
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    public function addRoutes(RouteCollection $routeCollection): RouteCollection
    {
        $routeCollection = $this->addUnzerPaymentResultRoute($routeCollection);
        $routeCollection = $this->addUnzerNotificationRoute($routeCollection);

        return $routeCollection;
    }

    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    protected function addUnzerPaymentResultRoute(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/unzer/payment-result', 'Unzer', 'Callback', 'paymentResultAction');
        $route = $route->setMethods(['GET']);

        $routeCollection->add(static::ROUTE_NAME_UNZER_PAYMENT_RESULT, $route);

        return $routeCollection;
    }

    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    protected function addUnzerNotificationRoute(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/unzer/notification', 'Unzer', 'Callback', 'notificationAction');
        $route = $route->setMethods(['POST']);

        $routeCollection->add(static::ROUTE_NAME_UNZER_NOTIFICATION, $route);

        return $routeCollection;
    }
}
