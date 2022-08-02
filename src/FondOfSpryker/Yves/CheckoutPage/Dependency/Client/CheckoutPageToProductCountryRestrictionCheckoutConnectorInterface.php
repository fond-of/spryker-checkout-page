<?php

namespace FondOfSpryker\Yves\CheckoutPage\Dependency\Client;

use Generated\Shared\Transfer\BlacklistedCountryCollectionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\BlacklistedCountryCollectionTransfer
     */
    public function getBlacklistedCountryCollectionByQuote(QuoteTransfer $quoteTransfer): BlacklistedCountryCollectionTransfer;
}
