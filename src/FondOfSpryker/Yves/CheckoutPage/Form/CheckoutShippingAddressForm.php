<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form;

use Symfony\Component\Form\FormBuilderInterface;

class CheckoutShippingAddressForm extends CheckoutBillingAddressForm
{
    public const FIELD_ADDITIONAL_ADDRESS = 'additional_address';

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

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function removeEmailField(FormBuilderInterface $builder)
    {
        $builder->remove(static::FIELD_EMAIL);

        return $this;
    }
}
