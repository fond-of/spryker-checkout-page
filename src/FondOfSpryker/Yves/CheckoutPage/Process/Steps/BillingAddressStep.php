<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Step\StepWithBreadcrumbInterface;

class BillingAddressStep extends AddressStep implements StepWithBreadcrumbInterface
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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function postCondition(AbstractTransfer $quoteTransfer): bool
    {
        if ($quoteTransfer->getBillingAddress() === null) {
            return false;
        }

        return $this->isAddressEmpty($quoteTransfer->getBillingAddress()) === false;
    }

    /**
     * @return string
     */
    public function getBreadcrumbItemTitle(): string
    {
        return static::BREADCRUMB_ITEM_TITLE;
    }
}
