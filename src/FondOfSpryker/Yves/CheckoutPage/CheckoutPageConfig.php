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

    /**
     * @return int
     */
    public function getFirstNameMinLength(): int
    {
        return $this->get(CheckoutPageConstants::CHECKOUT_PAGE_FIRST_NAME_VALIDATION_MIN_LENGTH_COUNT, 3);
    }

    /**
     * @return int
     */
    public function getLastNameMinLength(): int
    {
        return $this->get(CheckoutPageConstants::CHECKOUT_PAGE_LAST_NAME_VALIDATION_MIN_LENGTH_COUNT, 2);
    }

    /**
     * @return int
     */
    public function getDefaultMinLength(): int
    {
        return $this->get(CheckoutPageConstants::CHECKOUT_PAGE_DEFAULT_VALIDATION_MIN_LENGTH_COUNT, 3);
    }
}
