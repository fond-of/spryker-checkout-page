<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form\Steps;

use Generated\Shared\Transfer\QuoteTransfer;
use SprykerShop\Yves\CheckoutPage\Form\Steps\SummaryForm as SprykerShopSummaryForm;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SummaryForm extends SprykerShopSummaryForm
{
    /**
     * @var string
     */
    public const OPTION_ACCEPT_TERM_AND_CONDITIONS_LABEL = 'OPTION_ACCEPT_TERM_AND_CONDITIONS_LABEL';

    protected const FIELD_ACCEPT_TERMS_AND_CONDITIONS = QuoteTransfer::ACCEPT_TERMS_AND_CONDITIONS;

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $this->addAcceptTermsAndConditionsField($builder, $options);
        $this->addAcceptTerms($builder, $options);
        $this->addSignupNewsletter($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    protected function addAcceptTerms(FormBuilderInterface $builder, array $options)
    {
        $builder->add('acceptTerms', CheckboxType::class, [
            'required' => true,
            'label' => 'affenzahn.checkout.summaryForm.accept_terms',
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    protected function addSignupNewsletter(FormBuilderInterface $builder, array $options)
    {
        $builder->add('signupNewsletter', CheckboxType::class, [
            'required' => false,
            'label' => 'affenzahn.checkout.summaryForm.signup_newsletter',
        ]);
    }


    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(static::OPTION_ACCEPT_TERM_AND_CONDITIONS_LABEL);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return \SprykerShop\Yves\CheckoutPage\Form\Steps\SummaryForm
     */
    protected function addAcceptTermsAndConditionsField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_ACCEPT_TERMS_AND_CONDITIONS, CheckboxType::class, [
            'label' => $options[static::OPTION_ACCEPT_TERM_AND_CONDITIONS_LABEL],
            'required' => true,
        ]);

        return $this;
    }
}
