<?php

namespace FondOfSpryker\Yves\CheckoutPage\Dependency;

interface CheckoutStoreCountryDataProviderInterface
{
    /**
     * @return array
     */
    public function getCountries(): array;
}
