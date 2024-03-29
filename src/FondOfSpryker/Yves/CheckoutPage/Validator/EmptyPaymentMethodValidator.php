<?php

namespace FondOfSpryker\Yves\CheckoutPage\Validator;

use Symfony\Component\HttpFoundation\Request;

class EmptyPaymentMethodValidator implements RequestValidatorInterface
{
    /**
     * @var string
     */
    protected const PAYMENT_FORM_NAME = 'paymentForm';

    /**
     * @var string
     */
    protected const PAYMENT_FORM_SELECTION_NAME = 'paymentSelection';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function validate(Request $request): Request
    {
        if ($request->request->has(static::PAYMENT_FORM_NAME)) {
            //ToDo validate if formData really is an array
            $formData = $request->get(static::PAYMENT_FORM_NAME);

            if (
                $formData !== null && // @phpstan-ignore-line
                 array_key_exists(static::PAYMENT_FORM_SELECTION_NAME, $formData) && // @phpstan-ignore-line
                 $formData[static::PAYMENT_FORM_SELECTION_NAME] === ''
            ) {
                $request = $this->cleanRequest($request);
            }
        }

        return $request;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function cleanRequest(Request $request): Request
    {
        foreach ($request->request->keys() as $key) {
            $request->request->remove($key);
        }

        return $request;
    }
}
