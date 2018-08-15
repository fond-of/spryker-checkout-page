<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form;

use Generated\Shared\Transfer\AddressTransfer;
use SprykerShop\Yves\CustomerPage\Form\CheckoutAddressCollectionForm as SprykerShopCheckoutAddressCollectionForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class CheckoutAddressCollectionForm extends SprykerShopCheckoutAddressCollectionForm
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addShippingAddressSubForm($builder, $options)
            ->addSameAsShipmentCheckbox($builder);
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
            'required' => false,
            'validation_groups' => function (FormInterface $form) {
                if (!$form->has(CheckoutAddressForm::FIELD_ID_CUSTOMER_ADDRESS) || !$form->get(CheckoutAddressForm::FIELD_ID_CUSTOMER_ADDRESS)->getData()) {
                    return [self::GROUP_SHIPPING_ADDRESS];
                }

                return false;
            },
            CheckoutAddressForm::OPTION_VALIDATION_GROUP => self::GROUP_SHIPPING_ADDRESS,
            CheckoutAddressForm::OPTION_ADDRESS_CHOICES => $options[self::OPTION_ADDRESS_CHOICES],
            CheckoutAddressForm::OPTION_COUNTRY_CHOICES => $options[self::OPTION_COUNTRY_CHOICES],
        ];

        $builder->add(self::FIELD_SHIPPING_ADDRESS, CheckoutAddressForm::class, $options);

        return $this;
    }
}
