<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form\Steps;
use SprykerShop\Yves\CheckoutPage\Form\Steps\SummaryForm as SprykerShopSummaryForm;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class SummaryForm extends SprykerShopSummaryForm
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addAcceptTerms($builder, $options);
        $this->addSignupNewsletter($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    protected function addAcceptTerms(FormBuilderInterface $builder, array $options)
    {
        $builder->add('acceptTerms', CheckboxType::class, [
            'required' => true,
            'label' => false,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    protected function addSignupNewsletter(FormBuilderInterface $builder, array $options)
    {
        $builder->add('signupNewsletter', CheckboxType::class, [
            'required' => false,
            'label' => false,
        ]);
    }
}
