<?php

namespace FondOfSpryker\Yves\CheckoutPage\Plugin\Provider;

use Silex\Application;
use SprykerShop\Yves\CheckoutPage\Plugin\Provider\CheckoutPageControllerProvider as SprykerShopCheckoutPageControllerProvider;

class CheckoutPageControllerProvider extends SprykerShopCheckoutPageControllerProvider
{
    const CHECKOUT_BILLING_ADDRESS = 'checkout-billing-address';
    const CHECKOUT_SHIPPING_ADDRESS = 'checkout-shipping-address';

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
            ->addCheckoutSuccessRoute();
    }

    /**
     * @return $this
     */
    protected function addBillingAddressStepRoute(): self
    {
        $this->createController('/{checkout}/billing-address', self::CHECKOUT_BILLING_ADDRESS, 'CheckoutPage', 'Checkout', 'billingAddress')
            ->assert('checkout', $this->getAllowedLocalesPattern() . 'checkout|checkout')
            ->value('checkout', 'checkout')
            ->method('GET|POST');

        return $this;
    }

    protected function addShippingAddressStepRoute(): self
    {
        $this->createController('/{checkout}/shipping-address', self::CHECKOUT_SHIPPING_ADDRESS, 'CheckoutPage', 'Checkout', 'shippingAddress')
            ->assert('checkout', $this->getAllowedLocalesPattern() . 'checkout|checkout')
            ->value('checkout', 'checkout')
            ->method('GET|POST');

        return $this;
    }
}
