<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process;

use FondOfSpryker\Yves\CheckoutPage\CheckoutPageDependencyProvider;
use FondOfSpryker\Yves\CheckoutPage\Plugin\Provider\CheckoutPageControllerProvider;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\AddressStep\BillingAddressStepExecutor;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\AddressStep\ShippingAddressStepExecutor;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\BillingAddressStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\CustomerStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\PaymentStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\PlaceOrderStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\ShipmentStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\ShippingAddressStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\SuccessStep;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Yves\StepEngine\Process\StepCollection;
use SprykerShop\Yves\CheckoutPage\Process\StepFactory as SprykerShopStepFactory;
use SprykerShop\Yves\CheckoutPage\Process\Steps\AbstractBaseStep;
use SprykerShop\Yves\CheckoutPage\Process\Steps\StepExecutorInterface;
use SprykerShop\Yves\HomePage\Plugin\Provider\HomePageControllerProvider;

class StepFactory extends SprykerShopStepFactory
{
    use LoggerTrait;

    /**
     * @return \Spryker\Yves\StepEngine\Process\StepCollectionInterface
     */
    public function createStepCollection()
    {
        $stepCollection = new StepCollection(
            $this->getUrlGenerator(),
            CheckoutPageControllerProvider::CHECKOUT_ERROR,
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
            $this->getApplication()->path(HomePageControllerProvider::ROUTE_HOME),
        );
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Process\Steps\AbstractBaseStep
     */
    protected function createBillingAddressStep(): AbstractBaseStep
    {
        return new BillingAddressStep(
            $this->getCalculationClient(),
            CheckoutPageControllerProvider::CHECKOUT_BILLING_ADDRESS,
            HomePageControllerProvider::ROUTE_HOME,
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
            CheckoutPageControllerProvider::CHECKOUT_SHIPPING_ADDRESS,
            HomePageControllerProvider::ROUTE_HOME,
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
            CheckoutPageControllerProvider::CHECKOUT_SHIPMENT,
            HomePageControllerProvider::ROUTE_HOME,
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
            CheckoutPageControllerProvider::CHECKOUT_PAYMENT,
            $this->getConfig()->getEscapeRoute(),
            $this->getFlashMessenger(),
            $this->getCalculationClient(),
            $this->getCheckoutPaymentStepEnterPreCheckPlugins(),
            $this->getLogger()
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
            ],
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
            HomePageControllerProvider::ROUTE_HOME,
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
     */
    public function getCountryClient()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_COUNTRY);
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
}
