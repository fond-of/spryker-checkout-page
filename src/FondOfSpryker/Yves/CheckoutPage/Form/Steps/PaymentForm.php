<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form\Steps;

use SprykerShop\Yves\CheckoutPage\Form\Steps\PaymentForm as SprykerShopPaymentForm;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaymentForm extends SprykerShopPaymentForm
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $paymentMethodChoices
     *
     * @return $this
     */
    protected function addPaymentMethodChoices(FormBuilderInterface $builder, array $paymentMethodChoices)
    {
        $builder->add(
            self::PAYMENT_SELECTION,
            ChoiceType::class,
            [
                'choices' => $paymentMethodChoices,
                'label' => false,
                'required' => true,
                'property_path' => self::PAYMENT_SELECTION_PROPERTY_PATH,
                'choice_translation_domain' => 'global.payment.',
                'placeholder' => 'global.please_select',
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        return $this;
    }

    /**
     * @param array<\Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface> $paymentMethodSubForms
     *
     * @return array
     */
    protected function getPaymentMethodChoices(array $paymentMethodSubForms): array
    {
        $choices = [];

        foreach ($paymentMethodSubForms as $paymentMethodSubForm) {
            $subFormName = ucfirst($paymentMethodSubForm->getName());

            $choices[$subFormName] = $paymentMethodSubForm->getPropertyPath();
        }

        return $choices;
    }
}
