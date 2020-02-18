<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process;

use FondOfSpryker\Yves\CheckoutPage\CheckoutPageDependencyProvider;
use FondOfSpryker\Yves\CheckoutPage\Plugin\Provider\CheckoutPageControllerProvider;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\BillingAddressStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\CustomerStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\PlaceOrderStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\ShipmentStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\ShippingAddressStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\SuccessStep;
use FondOfSpryker\Yves\Shipment\ShipmentConfig;
use Spryker\Yves\StepEngine\Process\StepCollection;
use SprykerShop\Yves\CheckoutPage\Process\StepFactory as SprykerShopStepFactory;
use SprykerShop\Yves\CheckoutPage\Process\Steps\AbstractBaseStep;
use SprykerShop\Yves\HomePage\Plugin\Provider\HomePageControllerProvider;

class StepFactory extends SprykerShopStepFactory
{
    /**
     * @return \Spryker\Yves\StepEngine\Process\StepCollectionInterface
     */
    public function createStepCollection()
    {
        $stepCollection = new StepCollection(
            $this->getUrlGenerator(),
            CheckoutPageControllerProvider::CHECKOUT_ERROR
        );

        $stepCollection
            ->addStep($this->createEntryStep())
            ->addStep($this->createCustomerStep())
            ->addStep($this->createBillingAddressStep())
            ->addStep($this->createShippingAddresStep())
            ->addStep($this->createShipmentStep())
            ->addStep($this->createPaymentStep())
            ->addStep($this->createSummaryStep())
            ->addStep($this->createPlaceOrderStep())
            ->addStep($this->createSuccessStep());

        return $stepCollection;
    }

    /**
     * @return \FondOfSpryker\Yves\CheckoutPage\Process\Steps\CustomerStep
     */
    public function createCustomerStep()
    {
        return new CustomerStep(
            $this->getCustomerClient(),
            $this->getCustomerStepHandler(),
            CheckoutPageControllerProvider::CHECKOUT_CUSTOMER,
            HomePageControllerProvider::ROUTE_HOME,
            $this->getApplication()->path(HomePageControllerProvider::ROUTE_HOME)
        );
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Process\Steps\AbstractBaseStep
     */
    protected function createBillingAddressStep(): AbstractBaseStep
    {
        return new BillingAddressStep(
            $this->getCustomerClient(),
            $this->getCalculationClient(),
            $this->getCountryClient(),
            CheckoutPageControllerProvider::CHECKOUT_BILLING_ADDRESS,
            HomePageControllerProvider::ROUTE_HOME,
            $this->createAddressStepExecutor(),
            $this->createAddressStepPostConditionChecker(),
            $this->getConfig(),
            $this->getCheckoutAddressStepEnterPreCheckPlugins()
        );
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Process\Steps\AbstractBaseStep
     */
    public function createShippingAddresStep(): AbstractBaseStep
    {
        return new ShippingAddressStep(
            $this->getCustomerClient(),
            $this->getCalculationClient(),
            $this->getCountryClient(),
            CheckoutPageControllerProvider::CHECKOUT_SHIPPING_ADDRESS,
            HomePageControllerProvider::ROUTE_HOME,
            $this->createAddressStepExecutor(),
            $this->createShipmentStepPostConditionChecker(),
            $this->getConfig(),
            $this->getCheckoutAddressStepEnterPreCheckPlugins()
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
            CheckoutPageControllerProvider::CHECKOUT_SHIPMENT,
            HomePageControllerProvider::ROUTE_HOME,
            $this->getCheckoutShipmentStepEnterPreCheckPlugins(),
            $this->createShipmentConfig()
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
            $this->getStore()->getCurrentLocale(),
            $this->getGlossaryStorageClient(),
            CheckoutPageControllerProvider::CHECKOUT_PLACE_ORDER,
            HomePageControllerProvider::ROUTE_HOME,
            [
                'payment failed' => CheckoutPageControllerProvider::CHECKOUT_PAYMENT,
                'shipment failed' => CheckoutPageControllerProvider::CHECKOUT_SHIPMENT,
            ]
        );
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Process\Steps\SuccessStep
     */
    public function createSuccessStep()
    {
        return new SuccessStep(
            $this->getCustomerClient(),
            $this->getCartClient(),
            $this->getConfig(),
            $this->getPayoneClient(),
            $this->getSalesClient(),
            CheckoutPageControllerProvider::CHECKOUT_SUCCESS,
            HomePageControllerProvider::ROUTE_HOME
        );
    }

    /**
     * @return mixed
     */
    public function getPayoneClient()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_PAYONE);
    }

    /**
     * @return mixed
     */
    public function getSalesClient()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_SALES);
    }

    /**
     * @return mixed
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCountryClient()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_COUNTRY);
    }

    /**
     * @return \FondOfSpryker\Yves\Shipment\ShipmentConfig
     */
    protected function createShipmentConfig(): ShipmentConfig
    {
        return new ShipmentConfig();
    }
}
