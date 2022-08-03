<?php

namespace FondOfSpryker\Yves\CheckoutPage\Mapper;

use FondOfSpryker\Yves\CheckoutPage\Form\CheckoutBillingAddressForm;

class FormFieldNameMapper implements FormFieldNameMapperInterface
{
    /**
     * @var string
     */
    public const AUTOCOMPLETE_ATTR_EMAIL = 'email';

    /**
     * @var string
     */
    public const AUTOCOMPLETE_ATTR_SALUTATION = 'honorific-prefix';

    /**
     * @var string
     */
    public const AUTOCOMPLETE_ATTR_FIRST_NAME = 'given-name';

    /**
     * @var string
     */
    public const AUTOCOMPLETE_ATTR_LAST_NAME = 'family-name';

    /**
     * @var string
     */
    public const AUTOCOMPLETE_ATTR_ADDRESS_1 = 'address-line1';

    /**
     * @var string
     */
    public const AUTOCOMPLETE_ATTR_ADDRESS_2 = 'address-line2';

    /**
     * @var string
     */
    public const AUTOCOMPLETE_ATTR_ADDRESS_3 = 'address-line2';

    /**
     * @var string
     */
    public const AUTOCOMPLETE_ATTR_ZIP_CODE = 'postal-code';

    /**
     * @var string
     */
    public const AUTOCOMPLETE_ATTR_CITY = 'address-level2';

    /**
     * @var string
     */
    public const AUTOCOMPLETE_ATTR_ISO_2_CODE = 'country-name';

    /**
     * @var string
     */
    public const AUTOCOMPLETE_ATTR_PHONE = 'tel';

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var array
     */
    public const ATTR = [
        CheckoutBillingAddressForm::FIELD_EMAIL => self::AUTOCOMPLETE_ATTR_EMAIL,
        CheckoutBillingAddressForm::FIELD_SALUTATION => self::AUTOCOMPLETE_ATTR_SALUTATION,
        CheckoutBillingAddressForm::FIELD_FIRST_NAME => self::AUTOCOMPLETE_ATTR_FIRST_NAME,
        CheckoutBillingAddressForm::FIELD_LAST_NAME => self::AUTOCOMPLETE_ATTR_LAST_NAME,
        CheckoutBillingAddressForm::FIELD_ADDRESS_1 => self::AUTOCOMPLETE_ATTR_ADDRESS_1,
        CheckoutBillingAddressForm::FIELD_ADDRESS_2 => self::AUTOCOMPLETE_ATTR_ADDRESS_2,
        CheckoutBillingAddressForm::FIELD_ADDRESS_3 => self::AUTOCOMPLETE_ATTR_ADDRESS_3,
        CheckoutBillingAddressForm::FIELD_ZIP_CODE => self::AUTOCOMPLETE_ATTR_ZIP_CODE,
        CheckoutBillingAddressForm::FIELD_CITY => self::AUTOCOMPLETE_ATTR_CITY,
        CheckoutBillingAddressForm::FIELD_ISO_2_CODE => self::AUTOCOMPLETE_ATTR_ISO_2_CODE,
        CheckoutBillingAddressForm::FIELD_PHONE => self::AUTOCOMPLETE_ATTR_PHONE,
    ];

    /**
     * @param string|null $prefix
     */
    public function __construct(?string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param string $formFieldName
     *
     * @return string
     */
    public function mapFormFieldNameToAutocompletAttr(string $formFieldName): string
    {
        if (in_array(static::ATTR[$formFieldName], static::ATTR)) {
            return $this->prefix . ' ' . static::ATTR[$formFieldName];
        }

        return $formFieldName;
    }
}
