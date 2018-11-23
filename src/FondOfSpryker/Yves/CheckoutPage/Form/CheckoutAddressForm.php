<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form;

use SprykerShop\Yves\CustomerPage\Form\CheckoutAddressForm as SprykerShopCheckoutAddressForm;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;

class CheckoutAddressForm extends SprykerShopCheckoutAddressForm
{
    const FIELD_EMAIL = 'email';

    const VALIDATE_REGEX_EMAIL = "/^[A-ZÄÖÜa-zäöü0-9._%+\&\-ß!]+@[a-zäöüA-ZÄÖÜ0-9.\-ß]+\.[a-zäöüA-ZÄÖÜ]{2,}$/ix";

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addEmailField($builder, $options)
            ->addFirstNameField($builder, $options)
            ->addLastNameField($builder, $options)
            ->addAddress1Field($builder, $options)
            ->addAddress3Field($builder)
            ->addZipCodeField($builder, $options)
            ->addCityField($builder, $options)
            ->addIso2CodeField($builder, $options)
            ->addPhoneField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addEmailField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_EMAIL, EmailType::class, [
            'label' => 'customer.address.email',
            'required' => true,
            'constraints' => [
                $this->createEmailValidConstraints($options),
                $this->createRegexEmailConstraint($options),
                $this->createMinLengthConstraint($options),
            ],
        ]);

        return $this;
    }

    /**
     * @param array $options
     *
     * @return \Symfony\Component\Validator\Constraint
     */
    protected function createEmailValidConstraints(array $options): Constraint
    {
        return new Email([
            'message' => 'email.validation.error',
        ]);
    }

    /**
     * @param array $options
     *
     * @return \Symfony\Component\Validator\Constraint
     */
    protected function createRegexEmailConstraint(array $options): Constraint
    {
        $validationGroup = $this->getValidationGroup($options);

        return new Regex([
            'pattern' => static::VALIDATE_REGEX_EMAIL,
            'message' => 'validation.regex.email.message',
            'groups' => $validationGroup,
        ]);
    }
}
