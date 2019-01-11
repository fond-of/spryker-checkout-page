<?php

namespace FondOfSpryker\Yves\CheckoutPage\Dependency\Client;

use Generated\Shared\Transfer\CountryTransfer;

interface CheckoutPageToCountryInterface
{
    /**
     * @param \Generated\Shared\Transfer\CountryTransfer $countryTransfer
     *
     * @return \Generated\Shared\Transfer\CountryTransfer
     */
    public function getRegionsByCountryTransfer(CountryTransfer $countryTransfer): CountryTransfer;

    /**
     * @param string $iso2code
     *
     * @return \Generated\Shared\Transfer\CountryTransfer
     */
    public function getRegionByIso2Code(string $iso2code): CountryTransfer;
}
