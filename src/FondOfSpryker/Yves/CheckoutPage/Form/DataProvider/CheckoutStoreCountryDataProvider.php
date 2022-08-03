<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form\DataProvider;

use FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig;
use FondOfSpryker\Yves\CheckoutPage\Dependency\CheckoutStoreCountryDataProviderInterface;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Store;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToGlossaryStorageClientInterface;

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
    protected $config;

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface
     */
    protected $productCountryRestrictionCheckoutConnectorClient;

    /**
     * @var string
     */
    public const COUNTRY_GLOSSARY_PREFIX = 'countries.iso.';

    /**
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToGlossaryStorageClientInterface $glossaryStorageClient
     * @param \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface $productCountryRestrictionCheckoutConnectorClient
     * @param \Spryker\Shared\Kernel\Store $store
     * @param \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig $config
     */
    public function __construct(
        CheckoutPageToGlossaryStorageClientInterface $glossaryStorageClient,
        CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface $productCountryRestrictionCheckoutConnectorClient,
        Store $store,
        CheckoutPageConfig $config
    ) {
        $this->glossaryStorageClient = $glossaryStorageClient;
        $this->productCountryRestrictionCheckoutConnectorClient = $productCountryRestrictionCheckoutConnectorClient;
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
        $blacklistedCountryCollectionTransfer = $this->productCountryRestrictionCheckoutConnectorClient
            ->getBlacklistedCountryCollectionByQuote($quoteTransfer);

        $iso2codes = [];

        foreach ($blacklistedCountryCollectionTransfer->getBlacklistedCountries() as $blacklistedCountryTransfer) {
            $iso2codes[] = $blacklistedCountryTransfer->getIso2code();
        }

        if (strpos(strtoupper($this->store->getStoreName()), '_COM') !== false) {
            return array_diff_key($this->getComStoreCountries(), array_flip($iso2codes));
        }

        return array_diff_key($this->getDefault(), array_flip($iso2codes));
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
                $this->store->getCurrentLocale(),
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
                $this->store->getCurrentLocale(),
            );
        }

        asort($countries);

        return $countries;
    }
}
