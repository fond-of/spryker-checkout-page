<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form;

use Generated\Shared\Transfer\AddressTransfer;
use Spryker\Yves\Kernel\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @method \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig getConfig()
 */
class CheckoutShippingAddressCollectionForm extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_SHIPPING_ADDRESS = 'shippingAddress';

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
    public const OPTION_SALUTATION = 'salutations';

    public const GROUP_SHIPPING_ADDRESS = self::FIELD_SHIPPING_ADDRESS;

    /**
     * @var string
     */
    public const COUNTRY_CLIENT = 'country_client';

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'shippingAddressForm';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => [self::GROUP_SHIPPING_ADDRESS],
            self::OPTION_ADDRESS_CHOICES => [],
        ]);

        $resolver->setDefined(self::OPTION_ADDRESS_CHOICES);
        $resolver->setDefined(self::OPTION_SALUTATION);
        $resolver->setRequired(self::OPTION_COUNTRY_CHOICES);
        $resolver->setRequired(self::COUNTRY_CLIENT);
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
        $this->addShippingAddressSubForm($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addShippingAddressSubForm(FormBuilderInterface $builder, array $options)
    {
        $options = [
            'data_class' => AddressTransfer::class,
            'required' => true,
            'validation_groups' => [self::GROUP_SHIPPING_ADDRESS],
            CheckoutShippingAddressForm::OPTION_VALIDATION_GROUP => self::GROUP_SHIPPING_ADDRESS,
            CheckoutShippingAddressForm::OPTION_ADDRESS_CHOICES => $options[self::OPTION_ADDRESS_CHOICES],
            CheckoutShippingAddressForm::OPTION_COUNTRY_CHOICES => $options[self::OPTION_COUNTRY_CHOICES],
            CheckoutShippingAddressForm::COUNTRY_CLIENT => $options[self::COUNTRY_CLIENT],
            CheckoutShippingAddressForm::OPTION_SALUTATIONS => $options[self::OPTION_SALUTATION],
        ];

        $builder->add(self::FIELD_SHIPPING_ADDRESS, CheckoutShippingAddressForm::class, $options);

        return $this;
    }
}
