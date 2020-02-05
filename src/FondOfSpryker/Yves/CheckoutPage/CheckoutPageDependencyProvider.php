<?php

namespace FondOfSpryker\Yves\CheckoutPage;

use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryBridge;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientBridge;
use FondOfSpryker\Yves\CheckoutPage\Form\CheckoutBillingAddressCollectionForm;
use FondOfSpryker\Yves\CheckoutPage\Form\CheckoutShippingAddressCollectionForm;
use FondOfSpryker\Yves\CheckoutPage\Form\DataProvider\CheckoutBillingAddressFormDataProvider;
use FondOfSpryker\Yves\CheckoutPage\Form\DataProvider\CheckoutShippingAddressFormDataProvider;
use Generated\Shared\Transfer\PaymentTransfer;
use Spryker\Yves\Kernel\Container;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use SprykerEco\Yves\Payone\Plugin\PayoneCreditCardSubFormPlugin;
use SprykerEco\Yves\Payone\Plugin\PayoneEWalletSubFormPlugin;
use SprykerEco\Yves\Payone\Plugin\PayoneHandlerPlugin;
use SprykerShop\Yves\CheckoutPage\CheckoutPageDependencyProvider as SprykerShopCheckoutPageDependencyProvider;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToProductBundleClientBridge;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToShipmentClientBridge;
use SprykerShop\Yves\CustomerPage\Form\CheckoutAddressCollectionForm;
use SprykerShop\Yves\CustomerPage\Form\DataProvider\CheckoutAddressFormDataProvider;
use SprykerShop\Yves\MultiCartWidget\Plugin\ShopUi\MiniCartWidgetPlugin;

class CheckoutPageDependencyProvider extends SprykerShopCheckoutPageDependencyProvider
{
    public const BILLING_ADDRESS_STEP_SUB_FORM = 'BILLING_ADDRESS_STEP_SUB_FORM';
    public const BILLING_ADDRESS_FORM_DATA_PROVIDER = 'BILLING_ADDRESS_FORM_DATA_PROVIDER';

    public const SHIPPING_ADDRESS_STEP_SUB_FORM = 'SHIPPING_ADDRESS_STEP_SUB_FORM';
    public const SHIPPING_ADDRESS_FORM_DATA_PROVIDER = 'SHIPPING_ADDRESS_FORM_DATA_PROVIDER';

    public const CLIENT_PAYONE = 'CLIENT_PAYONE';
    public const CLIENT_SALES = 'CLIENT_SALES';
    public const CLIENT_COUNTRY = 'CLIENT_COUNTRY';

    public const PLUGIN_BILLING_ADDRESS_PAGE_WIDGETS = 'PLUGIN_BILLING_ADDRESS_PAGE_WIDGETS';

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
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
//        $container = $this->addGlossaryClient($container);
        $container = $this->addPriceClient($container);
        $container = $this->addProductBundleClient($container);

        $container = $this->addApplication($container);
        $container = $this->provideStore($container);
        $container = $this->addUtilValidateService($container);

        $container = $this->addSubFormPluginCollection($container);
        $container = $this->addPaymentMethodHandlerPluginCollection($container);
        $container = $this->extendPaymentMethodHandler($container);
        $container = $this->extendPaymentSubForms($container);
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
        $container = $this->addShippingAddressStepSubForm($container);
        $container = $this->addShippingAddressFormDataProvider($container);

        $container = $this->addPayoneClient($container);
        $container = $this->addSalesClient($container);
        $container = $this->addCustomerPageWidgetPlugins($container);
        $container = $this->addCountryClient($container);

        return $container;
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addPayoneClient(Container $container): Container
    {
        $container[self::CLIENT_PAYONE] = function (Container $container) {
            return $container->getLocator()->payone()->client();
        };

        return $container;
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addSalesClient(Container $container): Container
    {
        $container[self::CLIENT_SALES] = function (Container $container) {
            return $container->getLocator()->sales()->client();
        };

        return $container;
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
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
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCustomerClient(Container $container): Container
    {
        $container[self::CLIENT_CUSTOMER] = function (Container $container) {
            return new CheckoutPageToCustomerClientBridge($container->getLocator()->customer()->client());
        };

        return $container;
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCountryClient(Container $container): Container
    {
        $container[self::CLIENT_COUNTRY] = function (Container $container) {
            return new CheckoutPageToCountryBridge($container->getLocator()->country()->client());
        };

        return $container;
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryBridge
     */
    protected function getCountryClient(Container $container)
    {
        return new CheckoutPageToCountryBridge($container->getLocator()->country()->client());
    }

    /**
     * @return string[]
     */
    protected function getAddressStepSubForms(): array
    {
        return [
            CheckoutAddressCollectionForm::class,
        ];
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \SprykerShop\Yves\CustomerPage\Form\DataProvider\CheckoutAddressFormDataProvider
     */
    protected function getAddressStepFormDataProvider(Container $container): CheckoutAddressFormDataProvider
    {
        return new CheckoutAddressFormDataProvider(
            $this->getCustomerClient($container),
            $this->getStore(),
            $this->getCustomerService(),
            $this->getShipmentClient(),
            $this->getProductBundleClient()
        );
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
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
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addBillingAddressFormDataProvider(Container $container): Container
    {
        $container[self::BILLING_ADDRESS_FORM_DATA_PROVIDER] = function (Container $container) {
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
            CheckoutBillingAddressCollectionForm::class,
        ];
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \FondOfSpryker\Yves\CheckoutPage\Form\DataProvider\CheckoutBillingAddressFormDataProvider
     */
    protected function getBillingAddressFormDataProvider(Container $container): CheckoutBillingAddressFormDataProvider
    {
        return new CheckoutBillingAddressFormDataProvider(
            $this->getCustomerClient($container),
            $this->getCountryClient($container),
            $this->getStore()
        );
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addShippingAddressStepSubForm(Container $container): Container
    {
        $container[self::SHIPPING_ADDRESS_STEP_SUB_FORM] = function () {
            return $this->getShippingAddressStepSubForm();
        };

        return $container;
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addShippingAddressFormDataProvider(Container $container): Container
    {
        $container[self::SHIPPING_ADDRESS_FORM_DATA_PROVIDER] = function (Container $container) {
            return $this->getShippingAddressFormDataProvider($container);
        };

        return $container;
    }

    /**
     * @return array
     */
    protected function getShippingAddressStepSubForm(): array
    {
        return [
            CheckoutShippingAddressCollectionForm::class,
        ];
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \FondOfSpryker\Yves\CheckoutPage\Form\DataProvider\CheckoutShippingAddressFormDataProvider
     */
    protected function getShippingAddressFormDataProvider(Container $container): CheckoutShippingAddressFormDataProvider
    {
        return new CheckoutShippingAddressFormDataProvider(
            $this->getCustomerClient($container),
            $this->getCountryClient($container),
            $this->getStore()
        );
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function extendPaymentSubForms(Container $container): Container
    {
        $container->extend(self::PAYMENT_SUB_FORMS, function (SubFormPluginCollection $subFormPluginCollection) {
            $subFormPluginCollection->add(new PayoneCreditCardSubFormPlugin());
            $subFormPluginCollection->add(new PayoneEWalletSubFormPlugin());

            return $subFormPluginCollection;
        });

        return $container;
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function extendPaymentMethodHandler(Container $container): Container
    {
        $container->extend(self::PAYMENT_METHOD_HANDLER,
            function (StepHandlerPluginCollection $handlerPluginCollection) {
                $handlerPluginCollection->add(new PayoneHandlerPlugin(), PaymentTransfer::PAYONE_CREDIT_CARD);
                $handlerPluginCollection->add(new PayoneHandlerPlugin(), PaymentTransfer::PAYONE_E_WALLET);

                return $handlerPluginCollection;
            });

        return $container;
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addBillingAddressPageWidgetPlugins(Container $container): Container
    {
        $container[self::PLUGIN_BILLING_ADDRESS_PAGE_WIDGETS] = function () {
            return $this->getBillingAddressPageWidgetPlugins();
        };

        return $container;
    }

    /**
     * @return array
     */
    protected function getBillingAddressPageWidgetPlugins(): array
    {
        return [
            MiniCartWidgetPlugin::class,
        ];
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     * @return \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToShipmentClientBridge
     */
    protected function getShipmentClient(Container $container)
    {
        return new CheckoutPageToShipmentClientBridge($container->getLocator()->shipment()->client());
    }

    /**
     * @param  \Spryker\Yves\Kernel\Container  $container
     * @return \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToProductBundleClientBridge
     */
    protected function getProductBundleClient(Container $container)
    {
        return new CheckoutPageToProductBundleClientBridge($container->getLocator()->productBundle()->client());
    }
}
