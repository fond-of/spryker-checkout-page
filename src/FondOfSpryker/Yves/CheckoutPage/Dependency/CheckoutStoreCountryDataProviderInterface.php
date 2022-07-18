<?php

namespace FondOfSpryker\Yves\CheckoutPage\Dependency;

use Generated\Shared\Transfer\QuoteTransfer;

interface CheckoutStoreCountryDataProviderInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    public function getCountries(QuoteTransfer $quoteTransfer): array;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    public function getBlacklistedCountries(QuoteTransfer $quoteTransfer): array;
}
