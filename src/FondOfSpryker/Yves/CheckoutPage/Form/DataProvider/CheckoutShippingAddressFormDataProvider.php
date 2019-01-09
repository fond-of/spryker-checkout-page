<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form\DataProvider;

use FondOfSpryker\Yves\CheckoutPage\Form\CheckoutAddressCollectionForm;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use SprykerShop\Yves\CustomerPage\Dependency\Client\CustomerPageToCustomerClientInterface;

class CheckoutShippingAddressFormDataProvider implements StepEngineFormDataProviderInterface
{
    public const OPTION_REGION_CHOICES = 'region_choices';
    public const COUNTRY_GLOSSARY_PREFIX = 'countries.iso.';

    /**
     * @var \SprykerShop\Yves\CustomerPage\Dependency\Client\CustomerPageToCustomerClientInterface
     */
    protected $customerClient;

    /**
     * @var \Spryker\Shared\Kernel\Store
     */
    protected $store;

    /**
     * @param \SprykerShop\Yves\CustomerPage\Dependency\Client\CustomerPageToCustomerClientInterface $customerClient
     * @param \Spryker\Shared\Kernel\Store $store
     */
    public function __construct(CustomerPageToCustomerClientInterface $customerClient, Store $store)
    {
        $this->customerClient = $customerClient;
        $this->store = $store;
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function getData(AbstractTransfer $quoteTransfer)
    {
        $quoteTransfer->setBillingAddress($this->getBillingAddress($quoteTransfer));
        return $quoteTransfer;
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    public function getOptions(AbstractTransfer $quoteTransfer)
    {
        return [
            CheckoutAddressCollectionForm::OPTION_ADDRESS_CHOICES => $this->getAddressChoices(),
            CheckoutAddressCollectionForm::OPTION_COUNTRY_CHOICES => $this->getAvailableCountries(),
            //self::OPTION_REGION_CHOICES => $this->getRegionChoices(),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    protected function getRegionChoices(QuoteTransfer $quoteTransfer): array
    {
        return [];
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function getBillingAddress(QuoteTransfer $quoteTransfer)
    {
        $billingAddressTransfer = new AddressTransfer();
        if ($quoteTransfer->getBillingAddress() !== null) {
            $billingAddressTransfer = $quoteTransfer->getBillingAddress();
        }

        $this->customerClient->markCustomerAsDirty();

        $customerTransfer = $this->customerClient->getCustomer();

        if ($customerTransfer !== null && $quoteTransfer->getBillingAddress() === null) {
            $billingAddressTransfer->setIdCustomerAddress($customerTransfer->getDefaultBillingAddress());
        }

        return $billingAddressTransfer;
    }

    /**
     * @return array
     */
    protected function getAddressChoices()
    {
        $this->customerClient->markCustomerAsDirty();

        $customerTransfer = $this->customerClient->getCustomer();

        if ($customerTransfer === null) {
            return [];
        }

        $customerAddressesTransfer = $customerTransfer->getAddresses();

        if ($customerAddressesTransfer === null) {
            return [];
        }

        $choices = [];
        foreach ($customerAddressesTransfer->getAddresses() as $address) {
            $choices[$address->getIdCustomerAddress()] = sprintf(
                '%s %s %s, %s %s, %s %s',
                $address->getSalutation(),
                $address->getFirstName(),
                $address->getLastName(),
                $address->getAddress1(),
                $address->getAddress2(),
                $address->getZipCode(),
                $address->getCity()
            );
        }

        return $choices;
    }

    /**
     * @return array
     */
    protected function getAvailableCountries(): array
    {
        $countries = [];

        foreach ($this->store->getCountries() as $iso2Code) {
            $countries[$iso2Code] = self::COUNTRY_GLOSSARY_PREFIX . $iso2Code;
        }

        return $countries;
    }
}
