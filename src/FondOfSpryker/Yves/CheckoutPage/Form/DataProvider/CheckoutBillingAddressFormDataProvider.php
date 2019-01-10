<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form\DataProvider;

use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryInterface;
use FondOfSpryker\Yves\CheckoutPage\Form\CheckoutBillingAddressForm;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CountryTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use SprykerShop\Yves\CustomerPage\Dependency\Client\CustomerPageToCustomerClientInterface;

class CheckoutBillingAddressFormDataProvider implements StepEngineFormDataProviderInterface
{
    public const COUNTRY_GLOSSARY_PREFIX = 'countries.iso.';
    public const OPTION_REGION_CHOICES = 'region_choices';
    public const OPTION_ADDRESS_CHOICES = 'address_choices';
    public const OPTION_COUNTRY_CHOICES = 'country_choices';

    /**
     * @var \SprykerShop\Yves\CustomerPage\Dependency\Client\CustomerPageToCustomerClientInterface
     */
    protected $customerClient;

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryInterface
     */
    protected $countryClient;

    /**
     * @var \Spryker\Shared\Kernel\Store
     */
    protected $store;

    /**
     * @param \SprykerShop\Yves\CustomerPage\Dependency\Client\CustomerPageToCustomerClientInterface $customerClient
     * @param \Spryker\Shared\Kernel\Store $store
     */
    public function __construct(
        CustomerPageToCustomerClientInterface $customerClient,
        CheckoutPageToCountryInterface $countryClient,
        Store $store
    ) {
        $this->customerClient = $customerClient;
        $this->countryClient = $countryClient;
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
            CheckoutBillingAddressForm::OPTION_ADDRESS_CHOICES => $this->getAddressChoices(),
            CheckoutBillingAddressForm::OPTION_COUNTRY_CHOICES => $this->getAvailableCountries(),
            CheckoutBillingAddressForm::OPTION_REGION_CHOICES => $this->getRegionChoices($quoteTransfer),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    protected function getRegionChoices(QuoteTransfer $quoteTransfer): array
    {
        $countryTransfer = new CountryTransfer();
        $countryTransfer
            ->setIso2Code('DE')
            ->setIdCountry('60');

        $quoteTransfer->getBillingAddress()->setCountry($countryTransfer);

        if (!$quoteTransfer->getBillingAddress() instanceof AddressTransfer) {
            return [];
        }

        if ($quoteTransfer->getBillingAddress()->getCountry() === null) {
            return [];
        }

        $countryTransfer = $this->countryClient->getRegionsByCountryTransfer(
            $quoteTransfer->getBillingAddress()->getCountry()
        );

        $quoteTransfer->getBillingAddress()->setCountry($countryTransfer);

        /** @var \Generated\Shared\Transfer\RegionTransfer $region */
        foreach ($quoteTransfer->getBillingAddress()->getCountry()->getRegions() as $region) {
            $regions[$region->getIso2Code()] = $region->getName();
        }

        return $regions;
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
