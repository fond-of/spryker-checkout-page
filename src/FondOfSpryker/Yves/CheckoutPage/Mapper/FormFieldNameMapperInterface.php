<?php

namespace FondOfSpryker\Yves\CheckoutPage\Mapper;

interface FormFieldNameMapperInterface
{
    /**
     * @param string $formFieldName
     *
     * @return string
     */
    public function mapFormFieldNameToAutocompletAttr(string $formFieldName): string;
}
