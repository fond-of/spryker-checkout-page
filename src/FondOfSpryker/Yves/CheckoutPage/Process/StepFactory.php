<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process;

use FondOfSpryker\Shared\CheckoutPage\CheckoutPageConstants;
use FondOfSpryker\Yves\CheckoutPage\CheckoutPageDependencyProvider;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\AddressStep\BillingAddressStepExecutor;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\AddressStep\ShippingAddressStepExecutor;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\BillingAddressStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\CustomerStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\PaymentStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\PlaceOrderStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\ShipmentStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\ShippingAddressStep;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface as SprykerShopCheckoutPageToCustomerClientInterface;
use SprykerShop\Yves\CheckoutPage\Plugin\Router\CheckoutPageRouteProviderPlugin;
use SprykerShop\Yves\CheckoutPage\Process\StepFactory as SprykerShopStepFactory;
use SprykerShop\Yves\CheckoutPage\Process\Steps\AbstractBaseStep;
use SprykerShop\Yves\CheckoutPage\Process\Steps\StepExecutorInterface;

/**
 * @method \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig getConfig()
 */
class StepFactory extends SprykerShopStepFactory
{
    /**
     * @return array<\Spryker\Yves\StepEngine\Dependency\Step\StepInterface>
     */
    public function getSteps(): array
    {
        return [
            $this->createEntryStep(),
            $this->createCustomerStep(),
            $this->createBillingAddressStep(),
            $this->createShippingAddresStep(),
            $this->createShipmentStep(),
            $this->createPaymentStep(),
            $this->createSummaryStep(),
            $this->createPlaceOrderStep(),
            $this->createSuccessStep(),
            $this->createErrorStep(),
        ];
    }

    /**
     * @return \FondOfSpryker\Yves\CheckoutPage\Process\Steps\CustomerStep
     */
    public function createCustomerStep()
    {
        return new CustomerStep(
            $this->getCustomerClient(),
            $this->getCustomerStepHandler(),
            CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_CUSTOMER,
            $this->getConfig()->getEscapeRoute(),
            $this->getRouter()->generate(static::ROUTE_LOGOUT),
        );
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Process\Steps\AbstractBaseStep
     */
    protected function createBillingAddressStep(): AbstractBaseStep
    {
        return new BillingAddressStep(
            $this->getCalculationClient(),
            CheckoutPageConstants::ROUTE_CHECKOUT_BILLING_ADDRESS,
            $this->getConfig()->getEscapeRoute(),
            $this->createBillingAddressStepExecutor(),
            $this->createAddressStepPostConditionChecker(),
            $this->getConfig(),
            $this->getCheckoutAddressStepEnterPreCheckPlugins(),
            $this->createGiftCardItemsChecker(),
        );
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Process\Steps\AbstractBaseStep
     */
    public function createShippingAddresStep(): AbstractBaseStep
    {
        return new ShippingAddressStep(
            $this->getCalculationClient(),
            CheckoutPageConstants::ROUTE_CHECKOUT_SHIPPING_ADDRESS,
            $this->getConfig()->getEscapeRoute(),
            $this->createShippingAddressStepExecutor(),
            $this->createShipmentStepPostConditionChecker(),
            $this->getConfig(),
            $this->getCheckoutAddressStepEnterPreCheckPlugins(),
            $this->createGiftCardItemsChecker(),
        );
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Process\Steps\AbstractBaseStep
     */
    public function createShipmentStep(): AbstractBaseStep
    {
        return new ShipmentStep(
            $this->getCalculationClient(),
            $this->getShipmentPlugins(),
            $this->createShipmentStepPostConditionChecker(),
            $this->createGiftCardItemsChecker(),
            CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_SHIPMENT,
            $this->getConfig()->getEscapeRoute(),
            $this->getCheckoutShipmentStepEnterPreCheckPlugins(),
            $this->getConfig(),
        );
    }

    /**
     * @return \FondOfSpryker\Yves\CheckoutPage\Process\Steps\PaymentStep
     */
    public function createPaymentStep()
    {
        return new PaymentStep(
            $this->getPaymentClient(),
            $this->getPaymentMethodHandler(),
            CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_PAYMENT,
            $this->getConfig()->getEscapeRoute(),
            $this->getFlashMessenger(),
            $this->getCalculationClient(),
            $this->getCheckoutPaymentStepEnterPreCheckPlugins(),
            $this->createPaymentMethodKeyExtractor(),
        );
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Process\Steps\PlaceOrderStep
     */
    public function createPlaceOrderStep()
    {
        return new PlaceOrderStep(
            $this->getCheckoutClient(),
            $this->getFlashMessenger(),
            $this->getLocaleClient()->getCurrentLocale(),
            $this->getGlossaryStorageClient(),
            CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_PLACE_ORDER,
            $this->getConfig()->getEscapeRoute(),
            [
                static::ERROR_CODE_GENERAL_FAILURE => static::ROUTE_CART,
                'payment failed' => CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_PAYMENT,
                'shipment failed' => CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_SHIPMENT,
            ],
        );
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Process\Steps\StepExecutorInterface
     */
    public function createBillingAddressStepExecutor(): StepExecutorInterface
    {
        return new BillingAddressStepExecutor(
            $this->getCustomerService(),
            $this->getCustomerClient(),
            $this->getShoppingListItemExpanderPlugins(),
        );
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Process\Steps\StepExecutorInterface
     */
    public function createShippingAddressStepExecutor(): StepExecutorInterface
    {
        return new ShippingAddressStepExecutor(
            $this->getCustomerService(),
            $this->getCustomerClient(),
            $this->getShoppingListItemExpanderPlugins(),
        );
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface
     */
    public function getCustomerClient(): SprykerShopCheckoutPageToCustomerClientInterface
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_CUSTOMER);
    }
}
