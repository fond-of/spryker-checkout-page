<?php

namespace FondOfSpryker\Shared\CheckoutPage;

interface CheckoutPageConstants
{
    /**
     * @var string
     */
    public const SHOW_REGION_FOR_CONTRIES = 'SHOW_REGION_FOR_CONTRIES';

    /**
     * @var string
     */
    public const CHECKOUT_PAGE_FIRST_NAME_VALIDATION_MIN_LENGTH_COUNT = 'CHECKOUT_PAGE_FIRST_NAME_VALIDATION_MIN_LENGTH_COUNT';

    /**
     * @var string
     */
    public const CHECKOUT_PAGE_LAST_NAME_VALIDATION_MIN_LENGTH_COUNT = 'CHECKOUT_PAGE_LAST_NAME_VALIDATION_MIN_LENGTH_COUNT';

    /**
     * @var string
     */
    public const CHECKOUT_PAGE_DEFAULT_VALIDATION_MIN_LENGTH_COUNT = 'CHECKOUT_PAGE_DEFAULT_VALIDATION_MIN_LENGTH_COUNT';

    /**
     * @var string
     */
    public const CHECKOUT_PAGE_PRIORITY_COUNTRIES_COM_STORE = 'CHECKOUT_PAGE_PRIORITY_COUNTRIES_COM_STORE';

    /**
     * @var string
     */
    public const ROUTE_CHECKOUT_BILLING_ADDRESS = 'checkout-billing-address';

    /**
     * @var string
     */
    public const ROUTE_CHECKOUT_SHIPPING_ADDRESS = 'checkout-shipping-address';

    /**
     * @var string
     */
    public const ROUTE_CHECKOUT_REGION_BY_COUNTRY = 'checkout-region-by-country';

    /**
     * @var string
     */
    public const DEFAULT_SHIPMENT_METHOD_ID = 'FOND_OF_SPRYKER:CHECKOUT_PAGE:DEFAULT_SHIPMENT_METHOD_ID';

    /**
     * @var int
     */
    public const DEFAULT_SHIPMENT_METHOD_ID_VALUE = 1;
}
