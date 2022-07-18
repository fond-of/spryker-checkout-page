<?php

namespace FondOfSpryker\Yves\CheckoutPage\Dependency\Client;

use Generated\Shared\Transfer\BlacklistedCountryTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface CheckoutPageToProductCountryRestrictionCheckoutConnectorInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    public function getBlacklistedCountriesByQuote(QuoteTransfer $quoteTransfer): array;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\BlacklistedCountryTransfer
     */
    public function getBlacklistedCountries(QuoteTransfer $quoteTransfer): BlacklistedCountryTransfer;
}
