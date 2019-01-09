<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form;

use Generated\Shared\Transfer\AddressTransfer;
use Spryker\Yves\Kernel\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutShippingAddressCollectionForm extends AbstractType
{
    const FIELD_SHIPPING_ADDRESS = 'shippingAddress';
    const OPTION_ADDRESS_CHOICES = 'address_choices';
    const OPTION_COUNTRY_CHOICES = 'country_choices';
    const GROUP_SHIPPING_ADDRESS = self::FIELD_SHIPPING_ADDRESS;

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
        /** @var \Symfony\Component\OptionsResolver\OptionsResolver $resolver */
        $resolver->setDefaults([
            'validation_groups' => [self::GROUP_SHIPPING_ADDRESS],
            self::OPTION_ADDRESS_CHOICES => [],
        ]);

        $resolver->setDefined(self::OPTION_ADDRESS_CHOICES);
        $resolver->setRequired(self::OPTION_COUNTRY_CHOICES);
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
     * @return \FondOfSpryker\Yves\CheckoutPage\Form\CheckoutShippingAddressCollectionForm
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
        ];

        $builder->add(self::FIELD_SHIPPING_ADDRESS, CheckoutShippingAddressForm::class, $options);

        return $this;
    }
}
