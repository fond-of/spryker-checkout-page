<?php

namespace FondOfSpryker\Yves\CheckoutPage\Dependency\Client;

use FondOfOryx\Client\ProductCountryRestrictionCheckoutConnector\ProductCountryRestrictionCheckoutConnectorClient;
use Generated\Shared\Transfer\BlacklistedCountryCollectionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class CheckoutPageToProductCountryRestrictionCheckoutConnectorBridge implements CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface
{
    /**
     * @var \FondOfOryx\Client\ProductCountryRestrictionCheckoutConnector\ProductCountryRestrictionCheckoutConnectorClient
     */
    private $client;

    /**
     * @param \FondOfOryx\Client\ProductCountryRestrictionCheckoutConnector\ProductCountryRestrictionCheckoutConnectorClient $client
     */
    public function __construct(ProductCountryRestrictionCheckoutConnectorClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\BlacklistedCountryCollectionTransfer
     */
    public function getBlacklistedCountryCollectionByQuote(QuoteTransfer $quoteTransfer): BlacklistedCountryCollectionTransfer
    {
        return $this->client->getBlacklistedCountryCollectionByQuote($quoteTransfer);
    }
}
