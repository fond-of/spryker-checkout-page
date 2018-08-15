<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form;

use SprykerShop\Yves\CustomerPage\Form\CheckoutAddressForm as SprykerShopCheckoutAddressForm;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;

class CheckoutAddressForm extends SprykerShopCheckoutAddressForm
{
    const FIELD_EMAIL = 'email';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addEmailField($builder, $options)
            ->addFirstNameField($builder, $options)
            ->addLastNameField($builder, $options)
            ->addCompanyField($builder, $options)
            ->addAddress1Field($builder, $options)
            ->addAddress3Field($builder, $options)
            ->addZipCodeField($builder, $options)
            ->addCityField($builder, $options)
            ->addIso2CodeField($builder, $options)
            ->addPhoneField($builder, $options);
    }

    protected function addEmailField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_EMAIL, EmailType::class, [
            'label' => 'customer.address.email',
            'required' => true,
            'constraints' => [
                $this->createEmailValidConstraints($options)
            ]
        ]);

        return $this;
    }

    protected function createEmailValidConstraints(array $options)
    {
        return new Email([
            'message' => 'email.validation.error'
        ]);
    }
}
