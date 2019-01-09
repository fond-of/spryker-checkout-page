<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form;

use FondOfSpryker\Shared\Customer\CustomerConstants;
use Generated\Shared\Transfer\AddressTransfer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CheckoutBillingAddressForm extends CheckoutAddressForm
{
    const FIELD_REGION = 'region';
    const OPTION_REGION_CHOICES = 'region_choices';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $this->prepareEmailField($builder, $options);
        $this->preparePhoneField($builder, $options);
        $this->addRegionField($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $option
     *
     * @return $this
     */
    protected function addRegionField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_REGION, ChoiceType::class, [
            'label' => 'customer.address.region',
            'required' => true,
            'choices' => $options['region_choices'],
            'constraints' => [],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function prepareEmailField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_EMAIL, EmailType::class, [
            'label' => 'customer.email',
            'required' => true,
            'constraints' => [
                $this->createNotBlankConstraint($options),
                $this->createMinLengthConstraint($options),
                $this->createRegexEmailConstraint($options),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function preparePhoneField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_PHONE, TextType::class, [
            'label' => 'customer.address.phone',
            'required' => false,
            'constraints' => [
                $this->createPhoneNumberValidConstraint($options),
            ],
        ]);

        return $this;
    }

    /**
     * @param array $options
     *
     * @return \Symfony\Component\Validator\Constraints\Callback
     */
    protected function createPhoneNumberValidConstraint(array $options): Callback
    {
        return new Callback([
            'callback' => function ($object, ExecutionContextInterface $context) {
                /** @var \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer */
                $quoteTransfer = $context->getRoot()->getData();

                /** @var \Generated\Shared\Transfer\AddressTransfer $address */
                $address = $quoteTransfer->getBillingAddress();

                if (!$this->isPhoneNumberValid($address)) {
                    $context->buildViolation('checkout.error.field.notEU')
                        ->addViolation();
                }
            },
            'payload' => $this,
            'groups' => $options[self::OPTION_VALIDATION_GROUP],
        ]);
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return bool
     */
    protected function isPhoneNumberValid(AddressTransfer $addressTransfer): bool
    {
        $countriesInEU = CustomerConstants::COUNTRIES_IN_EU;
        $phoneNumber = $addressTransfer->getPhone();
        $countryIsoCode = $addressTransfer->getIso2Code();

        if ($phoneNumber === null && !\in_array($countryIsoCode, $countriesInEU, true)) {
            return false;
        }

        return true;
    }

    /**
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefined(self::OPTION_REGION_CHOICES);
        $resolver->setDefault(self::OPTION_REGION_CHOICES, []);
    }
}
