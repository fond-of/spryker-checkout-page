<?php

namespace FondOfSpryker\Yves\CheckoutPage;

use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryInterface;
use FondOfSpryker\Yves\CheckoutPage\Form\FormFactory;
use FondOfSpryker\Yves\CheckoutPage\Mapper\FormFieldNameMapper;
use FondOfSpryker\Yves\CheckoutPage\Mapper\FormFieldNameMapperInterface;
use FondOfSpryker\Yves\CheckoutPage\Process\StepFactory;
use FondOfSpryker\Yves\CheckoutPage\Validator\EmptyPaymentMethodValidator;
use FondOfSpryker\Yves\CheckoutPage\Validator\RequestValidatorInterface;
use SprykerShop\Yves\CheckoutPage\CheckoutPageFactory as SprykerShopCheckoutPageFactory;

/**
 * @method \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig getConfig()
 */
class CheckoutPageFactory extends SprykerShopCheckoutPageFactory
{
    /**
     * @return \Spryker\Yves\StepEngine\Process\StepEngineInterface
     */
    public function createCheckoutProcess()
    {
        return $this->createStepFactory()->createStepEngine(
            $this->createStepFactory()->createStepCollection()
        );
    }

    /**
     * @return \FondOfSpryker\Yves\CheckoutPage\Process\StepFactory
     */
    public function createStepFactory(): StepFactory
    {
        return new StepFactory();
    }

    /**
     * @return \FondOfSpryker\Yves\CheckoutPage\Form\FormFactory
     */
    public function createCheckoutFormFactory(): FormFactory
    {
        return new FormFactory();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection
     */
    public function getPaymentMethodSubForms()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::PAYMENT_SUB_FORMS);
    }

    /**
     * @return array<string>
     */
    public function getCustomerPageWidgetPlugins(): array
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::PLUGIN_CUSTOMER_PAGE_WIDGETS);
    }

    /**
     * @return \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig
     */
    public function getCheckoutPageConfig(): CheckoutPageConfig
    {
        return $this->getConfig();
    }

    /**
     * @return \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryInterface
     */
    public function getCountryClient(): CheckoutPageToCountryInterface
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_COUNTRY);
    }

    /**
     * @return \FondOfSpryker\Yves\CheckoutPage\Validator\RequestValidatorInterface
     */
    public function createEmptyPaymentMethodValidator(): RequestValidatorInterface
    {
        return new EmptyPaymentMethodValidator();
    }

    /**
     * @param string|null $prefix
     *
     * @return \FondOfSpryker\Yves\CheckoutPage\Mapper\FormFieldNameMapperInterface
     */
    public function createFormFieldNameMapper(?string $prefix): FormFieldNameMapperInterface
    {
        return new FormFieldNameMapper($prefix);
    }
}
