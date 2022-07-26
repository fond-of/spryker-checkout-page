<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form;

use FondOfSpryker\Shared\Customer\CustomerConstants;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CountryTransfer;
use Spryker\Yves\Kernel\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @method \FondOfSpryker\Yves\CheckoutPage\CheckoutPageFactory getFactory()
 * @method \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig getConfig()
 */
class CheckoutBillingAddressForm extends AbstractType
{
    public const FIELD_EMAIL = 'email';
    public const FIELD_SALUTATION = 'salutation';
    public const FIELD_FIRST_NAME = 'first_name';
    public const FIELD_LAST_NAME = 'last_name';
    public const FIELD_ADDRESS_1 = 'address1';
    public const FIELD_ADDRESS_2 = 'address2';
    public const FIELD_ADDRESS_3 = 'address3';
    public const FIELD_REGION = 'region';
    public const FIELD_PHONE = 'phone';
    public const FIELD_ZIP_CODE = 'zip_code';
    public const FIELD_CITY = 'city';
    public const FIELD_ISO_2_CODE = 'iso2_code';
    public const FIELD_ID_CUSTOMER_ADDRESS = 'id_customer_address';
    public const FIELD_SHOW_REGION = 'show_region';
    public const FIELD_HOUSE_NUMBER_VALIDATION = 'houseNumberValidation';

    public const OPTION_VALIDATION_GROUP = 'validation_group';

    public const OPTION_COUNTRY_CHOICES = 'country_choices';
    public const OPTION_ADDRESS_CHOICES = 'address_choices';
    public const OPTION_SALUTATIONS = 'salutations';
    public const OPTION_GIFT_CARD_ONLY_CARD = 'gift_card_only_card';

    protected const VALIDATION_NOT_BLANK_MESSAGE = 'validation.not_blank';
    protected const VALIDATION_MIN_LENGTH_MESSAGE = 'validation.min_length';
    protected const VALIDATE_REGEX_EMAIL = "/^[A-ZÄÖÜa-zäöü0-9._%+\&\-ß!]+@[a-zäöüA-ZÄÖÜ0-9.\-ß]+\.[a-zäöüA-ZÄÖÜ]{2,}$/ix";

    public const COUNTRY_CLIENT = 'country_client';
    public const AUTOCOMPLETE_PREFIX = 'billing';

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Mapper\FormFieldNameMapperInterface
     */
    protected $formFieldNameMapper;

    public function __construct()
    {
        $this->formFieldNameMapper = $this->getFactory()
            ->createFormFieldNameMapper(static::AUTOCOMPLETE_PREFIX);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            static::OPTION_ADDRESS_CHOICES => [],
            'allow_extra_fields' => true,
        ]);

        $resolver->setRequired(self::OPTION_COUNTRY_CHOICES);
        $resolver->setRequired(self::OPTION_VALIDATION_GROUP);
        $resolver->setRequired(self::COUNTRY_CLIENT);
        $resolver->setRequired(self::OPTION_SALUTATIONS);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addSalutationField($builder, $options)
            ->addFirstNameField($builder, $options)
            ->addLastNameField($builder, $options)
            ->addAddress1Field($builder, $options)
            ->addAddress2Field($builder)
            ->addAddress3Field($builder)
            ->addZipCodeField($builder, $options)
            ->addCityField($builder, $options)
            ->addIso2CodeField($builder, $options)
            ->addRegionField($builder, $options)
            ->addHouseNumberValidation($builder, $options)
            ->prepareEmailField($builder, $options)
            ->preparePhoneField($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addSalutationField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_SALUTATION, ChoiceType::class, [
            'label' => 'customer.address.salutation',
            'required' => false,
            'choices' => array_flip($options[self::OPTION_SALUTATIONS]),
            'choices_as_values' => true,
            'placeholder' => (count($options[self::OPTION_SALUTATIONS]) > 1) ? 'global.please_select' : false,
            'attr' => ['autocomplete' => $this->formFieldNameMapper->mapFormFieldNameToAutocompletAttr(self::FIELD_SALUTATION)],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addFirstNameField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_FIRST_NAME, TextType::class, [
            'label' => 'customer.address.first_name',
            'required' => true,
            'constraints' => [
                $this->createNotBlankConstraint($options),
                $this->createMinLengthConstraintFirstName($options),
            ],
            'attr' => [
                'autocomplete' => $this->formFieldNameMapper->mapFormFieldNameToAutocompletAttr(self::FIELD_FIRST_NAME),
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
    protected function addLastNameField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_LAST_NAME, TextType::class, [
            'label' => 'customer.address.last_name',
            'required' => true,
            'constraints' => [
                $this->createNotBlankConstraint($options),
                $this->createMinLengthConstraintLastName($options),
            ],
            'attr' => ['autocomplete' => $this->formFieldNameMapper->mapFormFieldNameToAutocompletAttr(self::FIELD_LAST_NAME)],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addHouseNumberValidation(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_HOUSE_NUMBER_VALIDATION, HiddenType::class, [
            'required' => true,
            'label' => false,
            'data' => '0',
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addAddress1Field(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_ADDRESS_1, TextType::class, [
            'label' => 'customer.address.address1',
            'required' => true,
            'constraints' => [
                $this->createNotBlankConstraint($options),
                $this->createMinLengthConstraintDefault($options),
                $this->createRegexHouseNumberConstraint($options),
            ],
            'attr' => ['autocomplete' => $this->formFieldNameMapper->mapFormFieldNameToAutocompletAttr(self::FIELD_ADDRESS_1)],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addAddress2Field(FormBuilderInterface $builder)
    {
        $builder->add(self::FIELD_ADDRESS_2, TextType::class, [
            'label' => 'customer.address.address2',
            'required' => false,
            'attr' => ['autocomplete' => $this->formFieldNameMapper->mapFormFieldNameToAutocompletAttr(self::FIELD_ADDRESS_2)],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addAddress3Field(FormBuilderInterface $builder)
    {
        $builder->add(self::FIELD_ADDRESS_3, TextType::class, [
            'label' => 'customer.address.address3',
            'required' => false,
            'attr' => ['autocomplete' => $this->formFieldNameMapper->mapFormFieldNameToAutocompletAttr(self::FIELD_ADDRESS_3)],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addZipCodeField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_ZIP_CODE, TextType::class, [
            'label' => 'customer.address.zip_code',
            'required' => true,
            'constraints' => [
                $this->createNotBlankConstraint($options),
            ],
            'attr' => ['autocomplete' => $this->formFieldNameMapper->mapFormFieldNameToAutocompletAttr(self::FIELD_ZIP_CODE)],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addCityField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_CITY, TextType::class, [
            'label' => 'customer.address.city',
            'required' => true,
            'constraints' => [
                $this->createNotBlankConstraint($options),
                $this->createMinLengthConstraintDefault($options),
            ],
            'attr' => ['autocomplete' => $this->formFieldNameMapper->mapFormFieldNameToAutocompletAttr(self::FIELD_CITY)],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addIso2CodeField(FormBuilderInterface $builder, array $options)
    {
        $selected = current(array_flip($options[self::OPTION_COUNTRY_CHOICES]));

        $builder->add(self::FIELD_ISO_2_CODE, ChoiceType::class, [
            'label' => 'customer.address.country',
            'required' => true,
            'choices' => array_flip($options[self::OPTION_COUNTRY_CHOICES]),
            'choices_as_values' => true,
            'placeholder' => (count($options[self::OPTION_COUNTRY_CHOICES]) > 1) ? 'global.please_select' : false,
            'empty_data' => (count($options[self::OPTION_COUNTRY_CHOICES]) === 1) ? $selected : false,
            'constraints' => [
                $this->createNotBlankConstraint($options),
            ],
            'attr' => ['autocomplete' => $this->formFieldNameMapper->mapFormFieldNameToAutocompletAttr(self::FIELD_ISO_2_CODE)],
        ]);

        return $this;
    }

    /**
     * @see https://symfony.com/doc/3.4/form/dynamic_form_modification.html
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addRegionField(FormBuilderInterface $builder, array $options)
    {
        if (count($options[self::OPTION_COUNTRY_CHOICES]) === 1) {
            $iso2code = ($builder->get(self::FIELD_ISO_2_CODE)->getData()
                ?: current(array_flip($options[self::OPTION_COUNTRY_CHOICES])));

            $builder->add(self::FIELD_REGION, ChoiceType::class, [
                'required' => true,
                'label' => 'customer.address.region',
                'choices' => array_flip($this->getRegions($iso2code)),
                'attr' => ['autocomplete' => $this->formFieldNameMapper->mapFormFieldNameToAutocompletAttr(self::FIELD_REGION)],
            ]);

            return $this;
        }

        $formModifier = function (FormInterface $form, ?string $iso2code = null) {
            $showRegions = $this->getFactory()
                ->getCheckoutPageConfig()
                ->getRegionsForCountries();

            if ($iso2code === null || !in_array(strtoupper($iso2code), $showRegions)) {
                if ($form->has(self::FIELD_REGION)) {
                    $form->remove(self::FIELD_REGION);
                }

                return $this;
            }

            $regions = $this->getRegions($iso2code);

            if (count($regions) > 0 && in_array(strtoupper($iso2code), $showRegions)) {
                $form->add(self::FIELD_REGION, ChoiceType::class, [
                    'required' => true,
                    'label' => 'customer.address.region',
                    'choices' => array_flip($regions),
                ]);
            } else {
                $form->remove(self::FIELD_REGION);
            }
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
            /** @var \Generated\Shared\Transfer\AddressTransfer|string|null $data */
            $data = $event->getData();

            $iso2code = $data instanceof AddressTransfer ? $data->getIso2Code() : $data;
            $formModifier($event->getForm(), $iso2code);
        });

        $builder->get(self::FIELD_ISO_2_CODE)->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formModifier) {
            $iso2code = $event->getForm()->getData();

            $formModifier($event->getForm()->getParent(), $iso2code);
        });

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
                $this->createMinLengthConstraintDefault($options),
                $this->createRegexEmailConstraint($options),
            ],
            'attr' => ['autocomplete' => $this->formFieldNameMapper->mapFormFieldNameToAutocompletAttr(self::FIELD_EMAIL)],
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
            'attr' => ['autocomplete' => $this->formFieldNameMapper->mapFormFieldNameToAutocompletAttr(self::FIELD_PHONE)],
        ]);

        return $this;
    }

    /**
     * @param array $options
     *
     * @return \Symfony\Component\Validator\Constraints\Callback
     */
    protected function createRegexHouseNumberConstraint(array $options): Callback
    {
        return new Callback([
            'callback' => function ($object, ExecutionContextInterface $context) {
                /** @var \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer */
                $quoteTransfer = $context->getRoot()->getData();
                $addressTransfer = $quoteTransfer->getBillingAddress();
                $pattern = '~[\d]+~';

                if ($addressTransfer->getHouseNumberValidation() === '1') {
                    return;
                }

                if (preg_match($pattern, $addressTransfer->getAddress1()) === 1) {
                    return;
                }

                $context->buildViolation('checkout.warning.field.housenumber')
                    ->addViolation();
            },
            'payload' => $this,
            'groups' => $options[self::OPTION_VALIDATION_GROUP],
        ]);
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

    /**
     * @param array $options
     *
     * @return \Symfony\Component\Validator\Constraints\NotBlank
     */
    protected function createNotBlankConstraint(array $options): NotBlank
    {
        return new NotBlank([
            'groups' => $options[static::OPTION_VALIDATION_GROUP],
            'message' => static::VALIDATION_NOT_BLANK_MESSAGE,
        ]);
    }

    /**
     * @param array $options
     * @param int|null $minLength
     *
     * @return \Symfony\Component\Validator\Constraints\Length
     */
    protected function createMinLengthConstraintDefault(array $options, ?int $minLength = null)
    {
        $validationGroup = $this->getValidationGroup($options);
        if ($minLength === null) {
            $minLength = $this->getConfig()->getDefaultMinLength();
        }

        return new Length([
            'min' => $minLength,
            'groups' => $validationGroup,
            'minMessage' => sprintf('%s_%s', static::VALIDATION_MIN_LENGTH_MESSAGE, $minLength),
        ]);
    }

    /**
     * @param array $options
     *
     * @return \Symfony\Component\Validator\Constraints\Length
     */
    protected function createMinLengthConstraintFirstName(array $options)
    {
        return $this->createMinLengthConstraintDefault($options, $this->getConfig()->getFirstNameMinLength());
    }

    /**
     * @param array $options
     *
     * @return \Symfony\Component\Validator\Constraints\Length
     */
    protected function createMinLengthConstraintLastName(array $options)
    {
        return $this->createMinLengthConstraintDefault($options, $this->getConfig()->getLastNameMinLength());
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

        if ($phoneNumber === null && !in_array($countryIsoCode, $countriesInEU, true)) {
            return false;
        }

        return true;
    }

    /**
     * @param array $options
     *
     * @return string
     */
    protected function getValidationGroup(array $options)
    {
        $validationGroup = Constraint::DEFAULT_GROUP;
        if (!empty($options['validation_group'])) {
            $validationGroup = $options['validation_group'];
        }

        return $validationGroup;
    }

    /**
     * @param string $iso2code
     *
     * @return array
     */
    protected function getRegions(string $iso2code): array
    {
        $regions = [];
        $countryClient = $this->getFactory()->getCountryClient();
        $countryTransfer = $countryClient->getRegionsByCountryTransfer((new CountryTransfer())->setIso2Code($iso2code));

        foreach ($countryTransfer->getRegions() as $region) {
            $regions[$region->getIso2Code()] = 'region.iso.' . $region->getIso2Code();
        }

        return $regions;
    }
}
