<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form;

use FondOfSpryker\Yves\CheckoutPage\CheckoutPageDependencyProvider;
use FondOfSpryker\Yves\CheckoutPage\Form\Steps\PaymentForm;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use Spryker\Yves\StepEngine\Form\FormCollectionHandler;
use SprykerShop\Yves\CheckoutPage\Form\FormFactory as SprykerShopFormFactory;

class FormFactory extends SprykerShopFormFactory
{
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
     * @param \Symfony\Component\Form\FormTypeInterface[] $formTypes
     * @param \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface|null $dataProvider
     *
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
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

    public function getBillingAddressFormDataProvider()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::BILLING_ADDRESS_FROM_DATA_PROVIDER);
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
}
