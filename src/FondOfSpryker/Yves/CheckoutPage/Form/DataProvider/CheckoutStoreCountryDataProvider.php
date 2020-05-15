<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form\DataProvider;

use FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig;
use FondOfSpryker\Yves\CheckoutPage\Dependency\CheckoutStoreCountryDataProviderInterface;
use Spryker\Shared\Kernel\Store;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToGlossaryStorageClientBridge;

class CheckoutStoreCountryDataProvider implements CheckoutStoreCountryDataProviderInterface
{
    /**
     * @var GlossaryStorageClientInterface
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

    public const COUNTRY_GLOSSARY_PREFIX = 'countries.iso.';

    /**
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToGlossaryStorageClientBridge $glossaryStorageClient
     * @param \Spryker\Shared\Kernel\Store $store
     * @param \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig $config
     */
    public function __construct(
        CheckoutPageToGlossaryStorageClientBridge $glossaryStorageClient,
        Store $store,
        CheckoutPageConfig $config
    ) {
        $this->glossaryStorageClient = $glossaryStorageClient;
        $this->store = $store;
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getCountries(): array
    {
        if (strpos(strtoupper($this->store->getStoreName()), '_COM') !== false) {
            return $this->getComStoreCountries();
        }

        return $this->getDefault();
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
