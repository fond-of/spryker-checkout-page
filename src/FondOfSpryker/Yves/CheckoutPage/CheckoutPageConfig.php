<?php

namespace FondOfSpryker\Yves\CheckoutPage;

use FondOfSpryker\Shared\CheckoutPage\CheckoutPageConstants;
use SprykerShop\Yves\CheckoutPage\CheckoutPageConfig as SprykerCheckoutPageConfig;

class CheckoutPageConfig extends SprykerCheckoutPageConfig
{
    /**
     * @return bool
     */
    public function cleanCartAfterOrderCreation()
    {
        return false;
    }

    /**
     * @return array
     */
    public function getRegionsForCountries(): array
    {
        return $this->get(CheckoutPageConstants::SHOW_REGION_FOR_CONTRIES, []);
    }
}
