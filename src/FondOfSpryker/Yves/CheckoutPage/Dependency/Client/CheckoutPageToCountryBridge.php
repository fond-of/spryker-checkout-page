<?php

namespace FondOfSpryker\Yves\CheckoutPage\Dependency\Client;

use FondOfSpryker\Client\Country\CountryClientInterface;
use Generated\Shared\Transfer\CountryTransfer;

class CheckoutPageToCountryBridge implements CheckoutPageToCountryInterface
{
    /**
     * @var \FondOfSpryker\Client\Country\CountryClientInterface
     */
    protected $countryClient;

    /**
     * @param \FondOfSpryker\Client\Country\CountryClientInterface $countryClient
     */
    public function __construct(CountryClientInterface $countryClient)
    {
        $this->countryClient = $countryClient;
    }

    /**
     * @param \Generated\Shared\Transfer\CountryTransfer $countryTransfer
     *
     * @return \Generated\Shared\Transfer\CountryTransfer
     */
    public function getRegionsByCountryTransfer(CountryTransfer $countryTransfer): CountryTransfer
    {
        return $this->countryClient->getRegionsByCountryTransfer($countryTransfer);
    }
}
