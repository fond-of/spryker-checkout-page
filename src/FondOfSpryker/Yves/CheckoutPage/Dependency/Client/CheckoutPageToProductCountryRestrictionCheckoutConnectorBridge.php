<?php

namespace FondOfSpryker\Yves\CheckoutPage\Dependency\Client;

use FondOfOryx\Client\ProductCountryRestrictionCheckoutConnector\ProductCountryRestrictionCheckoutConnectorClientInterface;
use Generated\Shared\Transfer\BlacklistedCountryTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class CheckoutPageToProductCountryRestrictionCheckoutConnectorBridge implements CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface
{
    /**
     * @var \FondOfOryx\Client\ProductCountryRestrictionCheckoutConnector\ProductCountryRestrictionCheckoutConnectorClientInterface
     */
    protected $client;

    /**
     * @param \FondOfOryx\Client\ProductCountryRestrictionCheckoutConnector\ProductCountryRestrictionCheckoutConnectorClientInterface $client
     */
    public function __construct(ProductCountryRestrictionCheckoutConnectorClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    public function getBlacklistedCountriesByQuote(QuoteTransfer $quoteTransfer): array
    {
        return $this->client->getBlacklistedCountriesByQuote($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\BlacklistedCountryTransfer
     */
    public function getBlacklistedCountries(QuoteTransfer $quoteTransfer): BlacklistedCountryTransfer
    {
        return $this->client->getBlacklistedCountries($quoteTransfer);
    }
}
