<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Step\StepWithBreadcrumbInterface;
use Symfony\Component\HttpFoundation\Request;

class BillingAddressStep extends AddressStep implements StepWithBreadcrumbInterface
{
    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     * @param \Generated\Shared\Transfer\AddressTransfer $billingAddress
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function updateCustomerDataFromBillingAddress(CustomerTransfer $customerTransfer, AddressTransfer $billingAddress)
    {
        $customerTransfer->fromArray($billingAddress->toArray(), true);

        return $customerTransfer;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer|void
     */
    public function execute(Request $request, AbstractTransfer $quoteTransfer)
    {
        $billingAddressTransfer = $quoteTransfer->getBillingAddress();
        $customerTransfer = $this->customerClient->getCustomer() ?? $quoteTransfer->getCustomer();
        $customerTransfer = $this->updateCustomerDataFromBillingAddress($customerTransfer, $billingAddressTransfer);

        if ($billingAddressTransfer !== null && $billingAddressTransfer->getIdCustomerAddress() !== null) {
            $billingAddressTransfer = $this->hydrateCustomerAddress(
                $billingAddressTransfer,
                $customerTransfer
            );

            $quoteTransfer->setBillingAddress($billingAddressTransfer);
        }

        if ($quoteTransfer->getBillingSameAsShipping() === true) {
            $quoteTransfer->setShippingAddress(clone $quoteTransfer->getBillingAddress());
        }

        $quoteTransfer->getBillingAddress()->setIsDefaultBilling(true);

        return $this->calculationClient->recalculate($quoteTransfer);
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

        $billingIsEmpty = $this->isAddressEmpty($quoteTransfer->getBillingAddress());

        if ($billingIsEmpty) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getBreadcrumbItemTitle()
    {
        return 'checkout.step.billing-address.title';
    }
}
