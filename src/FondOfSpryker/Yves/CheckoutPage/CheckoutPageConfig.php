<?php

namespace FondOfSpryker\Yves\CheckoutPage;

use FondOfSpryker\Shared\CheckoutPage\CheckoutPageConstants;
use SprykerShop\Yves\CheckoutPage\CheckoutPageConfig as SprykerCheckoutPageConfig;

class CheckoutPageConfig extends SprykerCheckoutPageConfig
{
    public const PAYMENT_METHOD_NAME_NO_PAYMENT = 'paid';

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

    /**
     * @return array
     */
    public function getPriorityCountriesComStore(): array
    {
        return $this->get(CheckoutPageConstants::CHECKOUT_PAGE_PRIORITY_COUNTRIES_COM_STORE, ['DE', 'AT', 'CH', 'FR', 'IT']);
    }

    /**
     * @return string
     */
    public function getDefaultShipmentMethodName(): string
    {
        return $this->get(
            CheckoutPageConstants::DEFAULT_SHIPMENT_METHOD_NAME,
            CheckoutPageConstants::DEFAULT_SHIPMENT_METHOD_NAME_VALUE
        );
    }
}
