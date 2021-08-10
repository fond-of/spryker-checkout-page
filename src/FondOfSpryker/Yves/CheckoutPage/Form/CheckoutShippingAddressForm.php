<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * @method \FondOfSpryker\Yves\CheckoutPage\CheckoutPageFactory getFactory()
 * @method \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig getConfig()
 */
class CheckoutShippingAddressForm extends CheckoutBillingAddressForm
{
    public const FIELD_ADDITIONAL_ADDRESS = 'additional_address';
    public const AUTOCOMPLETE_PREFIX = 'shipping';

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Mapper\FormFieldNameMapperInterface
     */
    protected $formFieldNameMapper;

    public function __construct()
    {
        parent::__construct();

        $this->formFieldNameMapper = $this->getFactory()
            ->createFormFieldNameMapper(static::AUTOCOMPLETE_PREFIX);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove(static::FIELD_EMAIL);
    }
}
