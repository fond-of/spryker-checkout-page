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
    protected function addPaymentMethodChoices(FormBuilderInterface $builder, array $paymentMethodChoices): PaymentForm
    {
        $builder->add(
            self::PAYMENT_SELECTION,
            ChoiceType::class,
            [
                'choices' => $paymentMethodChoices,
                'label' => false,
                'required' => true,
                'placeholder' => false,
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
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection|\Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[]
     */
    protected function getPaymentMethodSubForms(): array
    {
        $paymentMethodSubForms = [];

        foreach ($this->getFactory()->getPaymentMethodSubForms() as $paymentMethodSubFormPlugin) {
            $paymentMethodSubForms[] = $this->createSubForm($paymentMethodSubFormPlugin);
        }
        return $paymentMethodSubForms;
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[] $paymentMethodSubForms
     *
     * @return array
     */
    protected function getPaymentMethodChoices(array $paymentMethodSubForms): array
    {
        foreach ($paymentMethodSubForms as $paymentMethodSubForm) {
            $subFormName = ucfirst($paymentMethodSubForm->getName());

            if (!$paymentMethodSubForm instanceof SubFormProviderNameInterface) {
                $choices[$subFormName] = $paymentMethodSubForm->getPropertyPath();
                continue;
            }

            if (!isset($choices[$paymentMethodSubForm->getProviderName()])) {
                $choices[$paymentMethodSubForm->getProviderName()] = [];
            }

            $choices[$paymentMethodSubForm->getProviderName()][$subFormName] = $paymentMethodSubForm->getPropertyPath();
        }

        return $choices;
    }
}
