<?php

namespace FondOfSpryker\Yves\CheckoutPage;

use FondOfSpryker\Yves\CheckoutPage\Dependency\CheckoutStoreCountryDataProviderInterface;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryBridge;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientBridge;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToProductCountryRestrictionCheckoutConnectorBridge;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface;
use FondOfSpryker\Yves\CheckoutPage\Form\CheckoutBillingAddressCollectionForm;
use FondOfSpryker\Yves\CheckoutPage\Form\CheckoutShippingAddressCollectionForm;
use FondOfSpryker\Yves\CheckoutPage\Form\DataProvider\CheckoutBillingAddressFormDataProvider;
use FondOfSpryker\Yves\CheckoutPage\Form\DataProvider\CheckoutShippingAddressFormDataProvider;
use FondOfSpryker\Yves\CheckoutPage\Form\DataProvider\CheckoutStoreCountryDataProvider;
use Generated\Shared\Transfer\PaymentTransfer;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\Nopayment\NopaymentConfig;
use Spryker\Yves\Kernel\Container;
use Spryker\Yves\Nopayment\Plugin\NopaymentHandlerPlugin;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use SprykerEco\Yves\Payone\Plugin\PayoneCreditCardSubFormPlugin;
use SprykerEco\Yves\Payone\Plugin\PayoneEWalletSubFormPlugin;
use SprykerEco\Yves\Payone\Plugin\PayoneHandlerPlugin;
use SprykerShop\Yves\CheckoutPage\CheckoutPageDependencyProvider as SprykerShopCheckoutPageDependencyProvider;
use SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsChecker;
use SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface;
use SprykerShop\Yves\CustomerPage\Form\CheckoutAddressCollectionForm;

/**
 * @method \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig getConfig()
 */
class CheckoutPageDependencyProvider extends SprykerShopCheckoutPageDependencyProvider
{
    /**
     * @var string
     */
    public const BILLING_ADDRESS_STEP_SUB_FORM = 'BILLING_ADDRESS_STEP_SUB_FORM';

    /**
     * @var string
     */
    public const BILLING_ADDRESS_FORM_DATA_PROVIDER = 'BILLING_ADDRESS_FORM_DATA_PROVIDER';

    /**
     * @var string
     */
    public const SHIPPING_ADDRESS_STEP_SUB_FORM = 'SHIPPING_ADDRESS_STEP_SUB_FORM';

    /**
     * @var string
     */
    public const SHIPPING_ADDRESS_FORM_DATA_PROVIDER = 'SHIPPING_ADDRESS_FORM_DATA_PROVIDER';

    /**
     * @var string
     */
    public const CLIENT_PAYONE = 'CLIENT_PAYONE';

    /**
     * @var string
     */
    public const CLIENT_SALES = 'CLIENT_SALES';

    /**
     * @var string
     */
    public const CLIENT_COUNTRY = 'CLIENT_COUNTRY';

    /**
     * @var string
     */
    public const CLIENT_CUSTOMER = 'CLIENT_CUSTOMER';

    /**
     * @var string
     */
    public const CLIENT_PRODUCT_COUNTRY_RESTRICTION_CHECKOUT_CONNECTOR = 'CLIENT_PRODUCT_COUNTRY_RESTRICTION_CHECKOUT_CONNECTOR';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);

        $container = $this->addBillingAddressStepSubForm($container);
        $container = $this->addBillingAddressFormDataProvider($container);
        $container = $this->addShippingAddressStepSubForm($container);
        $container = $this->addShippingAddressFormDataProvider($container);
        $container = $this->addPayoneClient($container);
        $container = $this->addSalesClient($container);
        $container = $this->addCountryClient($container);
        $container = $this->extendPaymentMethodHandler($container);
        $container = $this->extendPaymentSubForms($container);
        $container = $this->addProductCountryRestrictionCheckoutConnectorClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addProductCountryRestrictionCheckoutConnectorClient(Container $container): Container
    {
        $self = $this;

        $container[static::CLIENT_PRODUCT_COUNTRY_RESTRICTION_CHECKOUT_CONNECTOR] = static function (Container $container) use ($self) {
            return $self->getProductCountryRestrictionCheckoutConnectorClient($container);
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface
     */
    protected function getProductCountryRestrictionCheckoutConnectorClient(
        Container $container
    ): CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface {
        return new CheckoutPageToProductCountryRestrictionCheckoutConnectorBridge(
            $container->getLocator()->productCountryRestrictionCheckoutConnector()->client()
        );
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
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
     * @param \Spryker\Yves\Kernel\Container $container
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
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCustomerClient(Container $container): Container
    {
        $self = $this;

        $container[self::CLIENT_CUSTOMER] = static function (Container $container) use ($self) {
            return $self->getCustomerClient($container);
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCountryClient(Container $container): Container
    {
        $self = $this;

        $container[self::CLIENT_COUNTRY] = static function (Container $container) use ($self) {
            return $self->getCountryClient($container);
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryBridge
     */
    protected function getCountryClient(Container $container)
    {
        return new CheckoutPageToCountryBridge($container->getLocator()->country()->client());
    }

    /**
     * @return array<string>
     */
    protected function getAddressStepSubForms(): array
    {
        return [
            CheckoutAddressCollectionForm::class,
        ];
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
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
     * @param \Spryker\Yves\Kernel\Container $container
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
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \FondOfSpryker\Yves\CheckoutPage\Form\DataProvider\CheckoutBillingAddressFormDataProvider
     */
    protected function getBillingAddressFormDataProvider(Container $container): CheckoutBillingAddressFormDataProvider
    {
        return new CheckoutBillingAddressFormDataProvider(
            $this->getCustomerClient($container),
            $this->getCountryClient($container),
            $this->getCheckoutStoreCountryProvider($container),
            $this->getGiftCardItemsChecker()
        );
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \FondOfSpryker\Yves\CheckoutPage\Dependency\CheckoutStoreCountryDataProviderInterface
     */
    protected function getCheckoutStoreCountryProvider(Container $container): CheckoutStoreCountryDataProviderInterface
    {
        return new CheckoutStoreCountryDataProvider(
            $container[static::CLIENT_GLOSSARY_STORAGE],
            $container[static::CLIENT_PRODUCT_COUNTRY_RESTRICTION_CHECKOUT_CONNECTOR],
            $this->getStore(),
            $this->getConfig()
        );
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
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
     * @param \Spryker\Yves\Kernel\Container $container
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
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \FondOfSpryker\Yves\CheckoutPage\Form\DataProvider\CheckoutShippingAddressFormDataProvider
     */
    protected function getShippingAddressFormDataProvider(Container $container): CheckoutShippingAddressFormDataProvider
    {
        return new CheckoutShippingAddressFormDataProvider(
            $this->getCustomerClient($container),
            $this->getCountryClient($container),
            $this->getCheckoutStoreCountryProvider($container),
            $this->getGiftCardItemsChecker()
        );
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
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
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function extendPaymentMethodHandler(Container $container): Container
    {
        $container->extend(
            self::PAYMENT_METHOD_HANDLER,
            function (StepHandlerPluginCollection $handlerPluginCollection) {
                $handlerPluginCollection->add(new PayoneHandlerPlugin(), PaymentTransfer::PAYONE_CREDIT_CARD);
                $handlerPluginCollection->add(new PayoneHandlerPlugin(), PaymentTransfer::PAYONE_E_WALLET);
                $handlerPluginCollection->add(new NopaymentHandlerPlugin(), NopaymentConfig::PAYMENT_METHOD_NAME);
                $handlerPluginCollection->add(new NopaymentHandlerPlugin(), NopaymentConfig::PAYMENT_PROVIDER_NAME);

                return $handlerPluginCollection;
            }
        );

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientBridge
     */
    protected function getCustomerClient(Container $container): CheckoutPageToCustomerClientInterface
    {
        return new CheckoutPageToCustomerClientBridge($container->getLocator()->customer()->client());
    }

    /**
     * @return \Spryker\Shared\Kernel\Store
     */
    protected function getStore(): Store
    {
        return Store::getInstance();
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface
     */
    protected function getGiftCardItemsChecker(): GiftCardItemsCheckerInterface
    {
        return new GiftCardItemsChecker();
    }
}
