<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class BillingAddressStep extends AddressStep
{
    public const BREADCRUMB_ITEM_TITLE = 'checkout.step.billing-address.title';

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $dataTransfer
     *
     * @return bool
     */
    public function preCondition(AbstractTransfer $dataTransfer): bool
    {
        return true;
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function postCondition(AbstractTransfer $quoteTransfer)
    {
        if ($quoteTransfer->getBillingAddress() === null) {
            return false;
        }

        return $this->isAddressEmpty($quoteTransfer->getBillingAddress()) === false;
    }

    /**
     * @return string
     */
    public function getBreadcrumbItemTitle()
    {
        return static::BREADCRUMB_ITEM_TITLE;
    }
}
