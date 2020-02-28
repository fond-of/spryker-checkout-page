<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form\DataProvider;

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
     * @var Store
     */
    protected $store;

    public const COUNTRY_GLOSSARY_PREFIX = 'countries.iso.';

    /**
     * @param \FondOfSpryker\Yves\CheckoutPage\Dependency\CheckoutStoreCountryDataProviderInterface $glossaryStorageClient
     * @param Store $store
     */
    public function __construct(CheckoutPageToGlossaryStorageClientBridge $glossaryStorageClient, Store $store)
    {
        $this->glossaryStorageClient = $glossaryStorageClient;
        $this->store = $store;
    }

    /**
     * @return array
     */
    public function getCountries(): array
    {
        if (\strpos(\strtoupper($this->store->getStoreName()), '_COM') !== false) {
            return $this->getComStoreCountries();
        }


        return $this->getDefault();
    }

    /**
     * @return array
     *
     * @throws
     */
    protected function getComStoreCountries()
    {
        $priorityCountriesIso2Codes = ['DE', 'AT', 'CH', 'FR', 'IT'];

        $allCountries = $this->getDefault();
        $priorityCountries = [];

        foreach($priorityCountriesIso2Codes as $iso2Code) {
            $priorityCountries[$iso2Code] = $this->glossaryStorageClient->translate(
                self::COUNTRY_GLOSSARY_PREFIX . $iso2Code,
                $this->store->getCurrentLocale()
            );

            if (\array_key_exists($iso2Code, $allCountries)) {
                unset($allCountries[$iso2Code]);
            }
        }

        return \array_merge($priorityCountries, $allCountries);
    }

    /**
     * @return array
     *
     * @throws
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

        \asort($countries);

        return $countries;
    }
}
