<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps\AddressStep;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerShop\Yves\CheckoutPage\Process\Steps\AddressStep\AddressStepExecutor as SprykerAddressStepExecutor;
use Symfony\Component\HttpFoundation\Request;

class BillingAddressStepExecutor extends SprykerAddressStepExecutor
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function execute(Request $request, QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $customerTransfer = $this->getCustomerTransfer() ?? $quoteTransfer->getCustomer();
        $customerTransfer = $this->updateCustomerDataFromBillingAddress($customerTransfer, $quoteTransfer);

        if ($quoteTransfer->getCustomer() === null) {
            $quoteTransfer->setCustomer($customerTransfer);
        }

        $quoteTransfer = $this->hydrateBillingAddressWithQuoteLevelData($quoteTransfer, $customerTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function updateCustomerDataFromBillingAddress(CustomerTransfer $customerTransfer, QuoteTransfer $quoteTransfer): CustomerTransfer
    {
        $billingAddress = $quoteTransfer->getBillingAddress();
        $customerTransfer->fromArray($billingAddress->toArray(), true);

        return $customerTransfer;
    }
}
