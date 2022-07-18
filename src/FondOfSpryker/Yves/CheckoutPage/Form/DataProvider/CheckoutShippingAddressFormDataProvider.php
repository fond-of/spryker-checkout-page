<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form\DataProvider;

use Generated\Shared\Transfer\QuoteTransfer;

class CheckoutShippingAddressFormDataProvider extends CheckoutBillingAddressFormDataProvider
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    protected function getAvailableCountries(QuoteTransfer $quoteTransfer): array
    {
        return $this->countryDataProvider->getCountries($quoteTransfer);
    }
}
