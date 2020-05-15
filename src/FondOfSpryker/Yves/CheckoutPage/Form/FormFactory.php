<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form;

use FondOfSpryker\Yves\CheckoutPage\CheckoutPageDependencyProvider;
use FondOfSpryker\Yves\CheckoutPage\Form\Steps\PaymentForm;
use FondOfSpryker\Yves\CheckoutPage\Form\Steps\SummaryForm;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use Spryker\Yves\StepEngine\Form\FormCollectionHandler;
use SprykerShop\Yves\CheckoutPage\Form\FormFactory as SprykerShopFormFactory;

class FormFactory extends SprykerShopFormFactory
{
    /**
     * @param array $formTypes
     * @param \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface|null $dataProvider
     *
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandler
     */
    public function createFormCollection(array $formTypes, ?StepEngineFormDataProviderInterface $dataProvider = null): FormCollectionHandler
    {
        return new FormCollectionHandler(
            $formTypes,
            $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY),
            $dataProvider
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     */
    public function createCustomerFormCollection()
    {
        return $this->createFormCollection($this->getCustomerFormTypes());
    }

    /**
     * @return string[]
     */
    public function getCustomerFormTypes()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CUSTOMER_STEP_SUB_FORMS);
    }

    /**
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     */
    public function createAddressFormCollection()
    {
        return $this->createFormCollection(
            $this->getAddressFormTypes(),
            $this->getAddressFormDataProvider()
        );
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    public function getAddressFormTypes()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::ADDRESS_STEP_SUB_FORMS);
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface|null
     */
    public function getAddressFormDataProvider()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::ADDRESS_STEP_FORM_DATA_PROVIDER);
    }

    /**
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandler|\Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     */
    public function createShippingAddressFormCollection()
    {
        return $this->createFormCollection(
            $this->getShippingAddressFormTypes(),
            $this->getShippingAddressFormDataProvider()
        );
    }

    /**
     * @return mixed
     */
    protected function getShippingAddressFormTypes()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::SHIPPING_ADDRESS_STEP_SUB_FORM);
    }

    /**
     * @return mixed
     */
    protected function getShippingAddressFormDataProvider()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::SHIPPING_ADDRESS_FORM_DATA_PROVIDER);
    }

    /**
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandler|\Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     */
    public function createBillingAddressFormCollection()
    {
        return $this->createFormCollection(
            $this->getBillingAddressFormTypes(),
            $this->getBillingAddressFormDataProvider()
        );
    }

    /**
     * @return mixed
     */
    public function getBillingAddressFormTypes()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::BILLING_ADDRESS_STEP_SUB_FORM);
    }

    /**
     * @return mixed
     */
    public function getBillingAddressFormDataProvider()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::BILLING_ADDRESS_FORM_DATA_PROVIDER);
    }

    /**
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     */
    public function getPaymentFormCollection()
    {
        $createPaymentSubForms = $this->getPaymentMethodSubForms();
        $subFormDataProvider = $this->createSubFormDataProvider($createPaymentSubForms);

        return $this->createSubFormCollection(PaymentForm::class, $subFormDataProvider);
    }

    /**
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     */
    public function createSummaryFormCollection()
    {
        return $this->createFormCollection($this->getSummaryFormTypes());
    }

    /**
     * @return string[]
     */
    public function getSummaryFormTypes()
    {
        return [
            $this->getSummaryForm(),
        ];
    }

    /**
     * @return string
     */
    public function getSummaryForm()
    {
        return SummaryForm::class;
    }
}
