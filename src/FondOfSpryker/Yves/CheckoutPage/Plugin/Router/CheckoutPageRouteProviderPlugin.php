<?php

namespace FondOfSpryker\Yves\CheckoutPage\Plugin\Router;

use FondOfSpryker\Shared\CheckoutPage\CheckoutPageConstants;
use Spryker\Yves\Router\Route\RouteCollection;
use SprykerShop\Yves\CheckoutPage\Plugin\Router\CheckoutPageRouteProviderPlugin as SprykerCheckoutPageRouteProviderPlugin;

class CheckoutPageRouteProviderPlugin extends SprykerCheckoutPageRouteProviderPlugin
{
    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    public function addRoutes(RouteCollection $routeCollection): RouteCollection
    {
        $routeCollection = parent::addRoutes($routeCollection);
        $routeCollection = $this->addBillingAddressStepRoute($routeCollection);
        $routeCollection = $this->addShippingAddressStepRoute($routeCollection);
        $routeCollection = $this->addRegionByCountry($routeCollection);

        return $routeCollection;
    }

    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    protected function addBillingAddressStepRoute(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/checkout/billing-address', 'CheckoutPage', 'Checkout', 'billingAddress');
        $route = $route->setMethods(['GET', 'POST']);
        $routeCollection->add(CheckoutPageConstants::ROUTE_CHECKOUT_BILLING_ADDRESS, $route);

        return $routeCollection;
    }

    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    protected function addShippingAddressStepRoute(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/checkout/shipping-address', 'CheckoutPage', 'Checkout', 'shippingAddress');
        $route = $route->setMethods(['GET', 'POST']);
        $routeCollection->add(CheckoutPageConstants::ROUTE_CHECKOUT_SHIPPING_ADDRESS, $route);

        return $routeCollection;
    }

    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    protected function addRegionByCountry(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute(
            '/checkout/region-by-country/{country}',
            'CheckoutPage',
            'Checkout',
            'regionsByCountry'
        );
        $route
            ->setMethods(['GET'])
            ->setRequirement('country', 'de');
        $routeCollection->add(CheckoutPageConstants::ROUTE_CHECKOUT_REGION_BY_COUNTRY, $route);

        return $routeCollection;
    }
}
