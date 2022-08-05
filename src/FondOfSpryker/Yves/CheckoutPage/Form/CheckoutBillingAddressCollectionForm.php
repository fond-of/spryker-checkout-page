<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form;

use Generated\Shared\Transfer\AddressTransfer;
use Spryker\Yves\Kernel\Form\AbstractType;
use SprykerShop\Yves\CustomerPage\Form\CheckoutAddressForm;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;

/**
 * @method \FondOfSpryker\Yves\CheckoutPage\CheckoutPageFactory getFactory()
 * @method \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig getConfig()
 */
class CheckoutBillingAddressCollectionForm extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_SHIPPING_ADDRESS = 'shippingAddress';

    /**
     * @var string
     */
    public const FIELD_BILLING_ADDRESS = 'billingAddress';

    /**
     * @var string
     */
    public const FIELD_BILLING_SAME_AS_SHIPPING = 'billingSameAsShipping';

    /**
     * @var string
     */
    public const OPTION_ADDRESS_CHOICES = 'address_choices';

    /**
     * @var string
     */
    public const OPTION_COUNTRY_CHOICES = 'country_choices';

    /**
     * @var string
     */
    public const OPTION_SALUTATIONS = 'salutations';

    /**
     * @var string
     */
    public const OPTION_GIFT_CARD_ONLY_CARD = 'gift_card_only_card';

    public const GROUP_SHIPPING_ADDRESS = self::FIELD_SHIPPING_ADDRESS;

    public const GROUP_BILLING_ADDRESS = self::FIELD_BILLING_ADDRESS;

    /**
     * @var string
     */
    public const COUNTRY_CLIENT = 'country_client';

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'billingAddressForm';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form) {
                $validationGroups = [Constraint::DEFAULT_GROUP, static::GROUP_BILLING_ADDRESS];

                if (!$form->get(static::FIELD_BILLING_SAME_AS_SHIPPING)->getData()) {
                    $validationGroups[] = static::GROUP_SHIPPING_ADDRESS;
                }

                return $validationGroups;
            },
            static::OPTION_ADDRESS_CHOICES => [],
        ]);

        $resolver->setDefined(static::OPTION_ADDRESS_CHOICES);
        $resolver->setDefined(static::OPTION_SALUTATIONS);
        $resolver->setRequired(static::COUNTRY_CLIENT);
        $resolver->setRequired(static::OPTION_COUNTRY_CHOICES);
        $resolver->setRequired(CheckoutBillingAddressForm::OPTION_GIFT_CARD_ONLY_CARD);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addSameAsShipmentCheckbox($builder, $options)
            ->addBillingAddressSubForm($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addSameAsShipmentCheckbox(FormBuilderInterface $builder, array $options)
    {
        $fieldType = CheckboxType::class;
        $fieldOptions = [
            'required' => false,
        ];

        if ($options[CheckoutBillingAddressForm::OPTION_GIFT_CARD_ONLY_CARD] === true) {
            $fieldType = HiddenType::class;
            $fieldOptions['data'] = 'true';
        }

        $builder->add(
            static::FIELD_BILLING_SAME_AS_SHIPPING,
            $fieldType,
            $fieldOptions
        );

        if ($options[CheckoutBillingAddressForm::OPTION_GIFT_CARD_ONLY_CARD] === true) {
            $builder->get(static::FIELD_BILLING_SAME_AS_SHIPPING)->addModelTransformer(
                new CallbackTransformer(function (bool $asBool) {
                    return (string)$asBool;
                }, function (?string $asString) {
                    return $asString === null ? true : (bool)$asString;
                })
            );
        }

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addBillingAddressSubForm(FormBuilderInterface $builder, array $options)
    {
        $options = [
            'data_class' => AddressTransfer::class,
            'validation_groups' => function (FormInterface $form) {
                if (!$form->has(CheckoutBillingAddressForm::FIELD_ID_CUSTOMER_ADDRESS) || !$form->get(CheckoutAddressForm::FIELD_ID_CUSTOMER_ADDRESS)->getData()) {
                    return [static::GROUP_BILLING_ADDRESS];
                }

                return false;
            },
            CheckoutBillingAddressForm::OPTION_VALIDATION_GROUP => static::GROUP_BILLING_ADDRESS,
            CheckoutBillingAddressForm::OPTION_ADDRESS_CHOICES => $options[static::OPTION_ADDRESS_CHOICES],
            CheckoutBillingAddressForm::OPTION_COUNTRY_CHOICES => $options[static::OPTION_COUNTRY_CHOICES],
            CheckoutBillingAddressForm::COUNTRY_CLIENT => $options[static::COUNTRY_CLIENT],
            CheckoutBillingAddressForm::OPTION_SALUTATIONS => $options[static::OPTION_SALUTATIONS],
        ];

        $builder->add(static::FIELD_BILLING_ADDRESS, CheckoutBillingAddressForm::class, $options);

        return $this;
    }
}
