<?php

namespace FondOfSpryker\Yves\CheckoutPage\Plugin\Provider;

use Silex\Application;
use SprykerShop\Yves\CheckoutPage\Plugin\Provider\CheckoutPageControllerProvider as SprykerShopCheckoutPageControllerProvider;

class CheckoutPageControllerProvider extends SprykerShopCheckoutPageControllerProvider
{
    const CHECKOUT_BILLING_ADDRESS = 'checkout-billing-address';
    const CHECKOUT_SHIPPING_ADDRESS = 'checkout-shipping-address';
    const CHECKOUT_REGION_BY_COUNTRY = 'checkout-region-by-country';

    /**
     * @param \Silex\Application $app
     *
     * @return void
     */
    protected function defineControllers(Application $app)
    {
        $this->addCheckoutIndexRoute()
            ->addCustomerStepRoute()
            ->addBillingAddressStepRoute()
            ->addShippingAddressStepRoute()
            ->addAddressStepRoute()
            ->addShipmentStepRoute()
            ->addPaymentStepRoute()
            ->addCheckoutSummaryStepRoute()
            ->addPlaceOrderStepRoute()
            ->addCheckoutErrorRoute()
            ->addCheckoutSuccessRoute()
            ->addRegionByCountry();
    }

    /**
     * @return \FondOfSpryker\Yves\CheckoutPage\Plugin\Provider\CheckoutPageControllerProvider
     */
    protected function addBillingAddressStepRoute(): self
    {
        $this->createController('/{checkout}/billing-address', self::CHECKOUT_BILLING_ADDRESS, 'CheckoutPage', 'Checkout', 'billingAddress')
            ->assert('checkout', $this->getAllowedLocalesPattern() . 'checkout|checkout')
            ->value('checkout', 'checkout')
            ->method('GET|POST');

        return $this;
    }

    /**
     * @return \FondOfSpryker\Yves\CheckoutPage\Plugin\Provider\CheckoutPageControllerProvider
     */
    protected function addShippingAddressStepRoute(): self
    {
        $this->createController('/{checkout}/shipping-address', self::CHECKOUT_SHIPPING_ADDRESS, 'CheckoutPage', 'Checkout', 'shippingAddress')
            ->assert('checkout', $this->getAllowedLocalesPattern() . 'checkout|checkout')
            ->value('checkout', 'checkout')
            ->method('GET|POST');

        return $this;
    }

    /**
     * @return \FondOfSpryker\Yves\CheckoutPage\Plugin\Provider\CheckoutPageControllerProvider
     */
    protected function addRegionByCountry(): self
    {
        $this->createController('/{checkout}/region-by-country/{country}', self::CHECKOUT_REGION_BY_COUNTRY, 'CheckoutPage', 'Checkout', 'getRegionsByCountry')
            ->assert('checkout', $this->getAllowedLocalesPattern() . 'checkout|checkout')
            ->value('checkout', 'checkout')
            ->value('country', 'de')
            ->method('GET');

        return $this;
    }
}
