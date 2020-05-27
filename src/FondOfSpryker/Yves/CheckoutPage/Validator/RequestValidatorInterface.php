<?php

namespace FondOfSpryker\Yves\CheckoutPage\Validator;

use Symfony\Component\HttpFoundation\Request;

interface RequestValidatorInterface
{
    /**
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function validate(Request $request): Request;
}
