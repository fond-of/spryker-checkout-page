<?php

namespace FondOfSpryker\Shared\CheckoutPage;

interface CheckoutPageConstants
{
    public const SHOW_REGION_FOR_CONTRIES = 'SHOW_REGION_FOR_CONTRIES';
    public const CHECKOUT_PAGE_FIRST_NAME_VALIDATION_MIN_LENGTH_COUNT = 'CHECKOUT_PAGE_FIRST_NAME_VALIDATION_MIN_LENGTH_COUNT';
    public const CHECKOUT_PAGE_LAST_NAME_VALIDATION_MIN_LENGTH_COUNT = 'CHECKOUT_PAGE_LAST_NAME_VALIDATION_MIN_LENGTH_COUNT';
    public const CHECKOUT_PAGE_DEFAULT_VALIDATION_MIN_LENGTH_COUNT = 'CHECKOUT_PAGE_DEFAULT_VALIDATION_MIN_LENGTH_COUNT';
    public const CHECKOUT_PAGE_PRIORITY_COUNTRIES_COM_STORE = 'CHECKOUT_PAGE_PRIORITY_COUNTRIES_COM_STORE';

    public const ROUTE_CHECKOUT_BILLING_ADDRESS = 'checkout-billing-address';
    public const ROUTE_CHECKOUT_SHIPPING_ADDRESS = 'checkout-shipping-address';
    public const ROUTE_CHECKOUT_REGION_BY_COUNTRY = 'checkout-region-by-country';

    public const DEFAULT_SHIPMENT_METHOD_NAME = 'FOND_OF_SPRYKER:CHECKOUT_PAGE:DEFAULT_SHIPMENT_METHOD_NAME';
    public const DEFAULT_SHIPMENT_METHOD_NAME_VALUE = 'Standard Shipment';
}
