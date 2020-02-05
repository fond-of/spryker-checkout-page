<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryInterface;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Step\StepWithBreadcrumbInterface;
use SprykerShop\Yves\CheckoutPage\CheckoutPageConfig;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\PostConditionCheckerInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\StepExecutorInterface;
use Symfony\Component\HttpFoundation\Request;

class BillingAddressStep extends AddressStep implements StepWithBreadcrumbInterface
{
    /**
     * @param  \Spryker\Shared\Kernel\Transfer\AbstractTransfer  $dataTransfer
     *
     * @return bool
     */
    public function preCondition(AbstractTransfer $dataTransfer): bool
    {
        return true;
    }

    /**
     * @param  \Generated\Shared\Transfer\CustomerTransfer  $customerTransfer
     * @param  \Generated\Shared\Transfer\AddressTransfer  $billingAddress
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function updateCustomerDataFromBillingAddress(
        CustomerTransfer $customerTransfer,
        AddressTransfer $billingAddress
    ) {
        $customerTransfer->fromArray($billingAddress->toArray(), true);

        return $customerTransfer;
    }

    /**
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * * @param  \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\QuoteTransfer  $quoteTransfer
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

        return $this->calculationClient->recalculate($quoteTransfer);
    }

    /**
     * @param  \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\QuoteTransfer  $quoteTransfer
     *
     * @return bool
     */
    public function postCondition(AbstractTransfer $quoteTransfer)
    {
        if ($quoteTransfer->getBillingAddress() === null) {
            return false;
        }

        return $this->isAddressEmpty($quoteTransfer->getBillingAddress());
    }

    /**
     * @return string
     */
    public function getBreadcrumbItemTitle()
    {
        return 'checkout.step.billing-address.title';
    }
}
