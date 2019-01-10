<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Step\StepWithBreadcrumbInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\AddressStep as SprykerShopAddressStep;
use Symfony\Component\HttpFoundation\Request;

class ShippingAddressStep extends SprykerShopAddressStep implements StepWithBreadcrumbInterface
{
    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $dataTransfer
     *
     * @return bool
     */
    public function requireInput(AbstractTransfer $dataTransfer): bool
    {
        /** @var \Generated\Shared\Transfer\QuoteTransfer $dataTransfer */
        return $dataTransfer->getBillingSameAsShipping() ? false : true;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer|\Spryker\Shared\Kernel\Transfer\AbstractTransfer|void
     */
    public function execute(Request $request, AbstractTransfer $quoteTransfer)
    {
        $shippingAddressTransfer = $quoteTransfer->getShippingAddress();
        $customerTransfer = $this->customerClient->getCustomer() ?? $quoteTransfer->getCustomer();

        $shippingAddressTransfer = $this->hydrateCustomerAddress(
            $shippingAddressTransfer,
            $customerTransfer
        );

        $quoteTransfer->setShippingAddress($shippingAddressTransfer);
        $quoteTransfer->getBillingAddress()->setIsDefaultBilling(true);

        return $this->calculationClient->recalculate($quoteTransfer);
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $quoteTransfer
     *
     * @return bool|void
     */
    public function postCondition(AbstractTransfer $quoteTransfer): bool
    {
        /** @var \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer */
        if ($quoteTransfer->getShippingAddress() === null) {
            return false;
        }

        $shippingIsEmpty = $quoteTransfer->getBillingSameAsShipping() === false && $this->isAddressEmpty($quoteTransfer->getShippingAddress());

        if ($shippingIsEmpty) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getBreadcrumbItemTitle()
    {
        return 'checkout.step.shipping-address.title';
    }
}
