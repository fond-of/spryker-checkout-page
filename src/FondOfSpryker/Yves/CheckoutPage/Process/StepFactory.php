<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process;

use FondOfSpryker\Yves\CheckoutPage\Plugin\Provider\CheckoutPageControllerProvider;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\BillingAddressStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\CustomerStep;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\ShippingAddressStep;
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
            CheckoutPageControllerProvider::CHECKOUT_BILLING_ADDRESS,
            HomePageControllerProvider::ROUTE_HOME
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
            CheckoutPageControllerProvider::CHECKOUT_SHIPPING_ADDRESS,
            HomePageControllerProvider::ROUTE_HOME
        );
    }
}
