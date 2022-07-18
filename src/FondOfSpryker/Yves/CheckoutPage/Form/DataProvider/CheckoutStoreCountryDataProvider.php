<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form\DataProvider;

use FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig;
use FondOfSpryker\Yves\CheckoutPage\Dependency\CheckoutStoreCountryDataProviderInterface;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Store;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToGlossaryStorageClientBridge;

class CheckoutStoreCountryDataProvider implements CheckoutStoreCountryDataProviderInterface
{
    /**
     * @var \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToGlossaryStorageClientBridge
     */
    protected $glossaryStorageClient;

    /**
     * @var \Spryker\Shared\Kernel\Store
     */
    protected $store;

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig
     */
    private $config;

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface
     */
    protected $countryRestrictionCheckoutConnectorClient;

    public const COUNTRY_GLOSSARY_PREFIX = 'countries.iso.';

    /**
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToGlossaryStorageClientBridge $glossaryStorageClient
     * @param \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface $countryRestrictionCheckoutConnectorClient
     * @param \Spryker\Shared\Kernel\Store $store
     * @param \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig $config
     */
    public function __construct(
        CheckoutPageToGlossaryStorageClientBridge $glossaryStorageClient,
        CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface $countryRestrictionCheckoutConnectorClient,
        Store $store,
        CheckoutPageConfig $config
    ) {
        $this->glossaryStorageClient = $glossaryStorageClient;
        $this->countryRestrictionCheckoutConnectorClient = $countryRestrictionCheckoutConnectorClient;
        $this->store = $store;
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    public function getCountries(QuoteTransfer $quoteTransfer): array
    {
        if (strpos(strtoupper($this->store->getStoreName()), '_COM') !== false) {
            return $this->getComStoreCountries();
        }

        return $this->getDefault();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    public function getBlacklistedCountries(QuoteTransfer $quoteTransfer): array
    {
        $blacklistedCountryTransfer = $this->countryRestrictionCheckoutConnectorClient->getBlacklistedCountries($quoteTransfer);

        if (count($blacklistedCountryTransfer->getIso2codes()) > 0) {
            $iso2codes = $this->getComStoreCountries();
            $validIso2Codes = [];

            foreach ($iso2codes as $iso2code => $country) {
                if (in_array($iso2code, $blacklistedCountryTransfer->getIso2codes())) {
                    continue;
                }

                $validIso2Codes[$iso2code] = $country;
            }

            return $validIso2Codes;
        }
    }

    /**
     * @return array
     */
    protected function getComStoreCountries()
    {
        $priorityCountriesIso2Codes = $this->config->getPriorityCountriesComStore();

        $allCountries = $this->getDefault();
        $priorityCountries = [];

        foreach ($priorityCountriesIso2Codes as $iso2Code) {
            $priorityCountries[$iso2Code] = $this->glossaryStorageClient->translate(
                self::COUNTRY_GLOSSARY_PREFIX . $iso2Code,
                $this->store->getCurrentLocale()
            );

            if (array_key_exists($iso2Code, $allCountries)) {
                unset($allCountries[$iso2Code]);
            }
        }

        return array_merge($priorityCountries, $allCountries);
    }

    /**
     * @return array
     */
    protected function getDefault(): array
    {
        $countries = [];

        foreach ($this->store->getCountries() as $iso2Code) {
            $countries[$iso2Code] = $this->glossaryStorageClient->translate(
                self::COUNTRY_GLOSSARY_PREFIX . $iso2Code,
                $this->store->getCurrentLocale()
            );
        }

        asort($countries);

        return $countries;
    }
}
