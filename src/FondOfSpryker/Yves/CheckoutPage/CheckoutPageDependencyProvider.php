<?php

namespace FondOfSpryker\Yves\CheckoutPage;

use FondOfSpryker\Yves\CustomerPage\Form\CheckoutBillingAddressCollectionForm;
use FondOfSpryker\Yves\CustomerPage\Form\DataProvider\CheckoutBillingAddressFormDataProvider;
use SprykerShop\Yves\CheckoutPage\CheckoutPageDependencyProvider as SprykerShopCheckoutPageDependencyProvider;
use Spryker\Yves\Kernel\Container;
use FondOfSpryker\Yves\CheckoutPage\Form\CheckoutAddressCollectionForm;
use SprykerShop\Yves\CustomerPage\Form\CustomerCheckoutForm;
use SprykerShop\Yves\CustomerPage\Form\DataProvider\CheckoutAddressFormDataProvider;
use SprykerShop\Yves\CustomerPage\Form\GuestForm;
use SprykerShop\Yves\CustomerPage\Form\LoginForm;
use SprykerShop\Yves\CustomerPage\Form\RegisterForm;
use Spryker\Yves\Kernel\Plugin\Pimple;

class CheckoutPageDependencyProvider extends SprykerShopCheckoutPageDependencyProvider
{
    const BILLING_ADDRESS_STEP_SUB_FORM = 'BILLING_ADDRESS_STEP_SUB_FORM';
    const BILLING_ADDRESS_FROM_DATA_PROVIDER = 'BILLING_ADDRESS_FROM_DATA_PROVIDER';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container)
    {
        $container = $this->addQuoteClient($container);
        $container = $this->addCalculationClient($container);
        $container = $this->addCheckoutClient($container);
        $container = $this->addCustomerClient($container);
        $container = $this->addCartClient($container);
        $container = $this->addShipmentClient($container);
        $container = $this->addGlossaryClient($container);
        $container = $this->addPriceClient($container);
        $container = $this->addProductBundleClient($container);

        $container = $this->addApplication($container);
        $container = $this->provideStore($container);
        $container = $this->addUtilValidateService($container);

        $container = $this->addSubFormPluginCollection($container);
        $container = $this->addPaymentMethodHandlerPluginCollection($container);
        $container = $this->addCustomerStepHandlerPlugin($container);
        $container = $this->addShipmentHandlerPluginCollection($container);
        $container = $this->addShipmentFormDataProviderPlugin($container);
        $container = $this->addMoneyPlugin($container);
        $container = $this->addCheckoutBreadcrumbPlugin($container);
        $container = $this->addCustomerPageWidgetPlugins($container);
        $container = $this->addAddressPageWidgetPlugins($container);
        $container = $this->addShipmentPageWidgetPlugins($container);
        $container = $this->addPaymentPageWidgetPlugins($container);
        $container = $this->addSummaryPageWidgetPlugins($container);
        $container = $this->addSummaryPageWidgetPlugins($container);
        $container = $this->addSuccessPageWidgetPlugins($container);

        $container = $this->addCustomerStepSubForms($container);
        $container = $this->addAddressStepSubForms($container);
        $container = $this->addAddressStepFormDataProvider($container);
        $container = $this->addGlossaryStorageClient($container);

        $container = $this->addBillingAddressStepSubForm($container);
        $container = $this->addBillingAddressFormDataProvider($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCustomerStepSubForms(Container $container): Container
    {
        $container[self::CUSTOMER_STEP_SUB_FORMS] = function () {
            return $this->getCustomerStepSubForms();
        };

        return $container;
    }

    /**
     * @return string[]
     */
    protected function getSummaryPageWidgetPlugins(): array
    {
        return [
            CheckoutVoucherFormWidgetPlugin::class,
            CartNoteQuoteItemNoteWidgetPlugin::class, #CartNoteFeature
            CartNoteQuoteNoteWidgetPlugin::class, #CartNoteFeature
        ];
    }

    /**
     * @return mixed[]
     */
    protected function getCustomerStepSubForms(): array
    {
        return [
            LoginForm::class,
            $this->getCustomerCheckoutForm(RegisterForm::class, RegisterForm::BLOCK_PREFIX),
            $this->getCustomerCheckoutForm(GuestForm::class, GuestForm::BLOCK_PREFIX),
        ];
    }

    /**
     * @param string $subForm
     * @param string $blockPrefix
     *
     * @return \SprykerShop\Yves\CustomerPage\Form\CustomerCheckoutForm|\Symfony\Component\Form\FormInterface
     */
    protected function getCustomerCheckoutForm($subForm, $blockPrefix)
    {
        return $this->getFormFactory()->createNamed(
            $blockPrefix,
            CustomerCheckoutForm::class,
            null,
            [CustomerCheckoutForm::SUB_FORM_CUSTOMER => $subForm]
        );
    }

    /**
     * @return \Pyz\Yves\CheckoutPage\Form\FormFactory
     */
    private function getFormFactory()
    {
        return (new Pimple())->getApplication()['form.factory'];
    }

    /**
     * @return string[]
     */
    protected function getAddressStepSubForms(): string
    {
        return [
            CheckoutAddressCollectionForm::class,
        ];
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface|null
     */
    protected function getAddressStepFormDataProvider(Container $container): ?StepEngineFormDataProviderInterface
    {
        return new CheckoutAddressFormDataProvider($this->getCustomerClient($container), $this->getStore());
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addBillingAddressStepSubForm(Container $container): Container
    {
        $container[self::BILLING_ADDRESS_STEP_SUB_FORM] = function () {
            return $this->getBillingAddressStepSubForm();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addBillingAddressFormDataProvider(Container $container): Container
    {
        $container[self::BILLING_ADDRESS_FROM_DATA_PROVIDER] = function (Container $container) {
            return $this->getBillingAddressFormDataProvider($container);
        };

        return $container;
    }

    /**
     * @return array
     */
    protected function getBillingAddressStepSubForm(): array
    {
        return [
            CheckoutBillingAddressCollectionForm::class
        ];
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     * @return \Pyz\Yves\CustomerPage\Form\DataProvider\CheckoutBillingAddressFormDataProvider
     */
    protected function getBillingAddressFormDataProvider(Container $container): CheckoutBillingAddressFormDataProvider
    {
        return new CheckoutBillingAddressFormDataProvider(
            $this->getCustomerClient($container),
            $this->getStore()
        );
    }
}
