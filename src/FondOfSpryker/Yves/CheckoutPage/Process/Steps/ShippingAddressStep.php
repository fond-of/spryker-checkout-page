<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientBridge;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Step\StepWithBreadcrumbInterface;
use SprykerShop\Yves\CheckoutPage\CheckoutPageConfig;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\PostConditionCheckerInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\StepExecutorInterface;
use Symfony\Component\HttpFoundation\Request;

class ShippingAddressStep extends AddressStep implements StepWithBreadcrumbInterface
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
        $customerTransfer = $this->customerClient->getCustomer() ?? $quoteTransfer->getCustomer();

        /** @var \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer */
        foreach ($quoteTransfer->getItems() as $item){
            $shipment = $item->getShipment();

            if ($shipment === null){
                $shipment = new ShipmentTransfer();
            }

            $shippingAddressTransfer = $shipment->getShippingAddress();

            if ($shippingAddressTransfer === null){
                $shippingAddressTransfer = new AddressTransfer();
            }

            $shippingAddressTransfer = $this->hydrateCustomerAddress(
                $shippingAddressTransfer,
                $customerTransfer
            );

            $shipment->setShippingAddress($shippingAddressTransfer);
            $item->setShipment($shipment);
        }
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

        foreach ($quoteTransfer->getItems() as $item) {
            $shipment = $item->getShipment();
            if ($shipment === null || $shipment->getShippingAddress() === null){
                return false;
            }

            if ($quoteTransfer->getBillingSameAsShipping() === false && $this->isAddressEmpty($shipment->getShippingAddress()) === true){
                return false;
            }
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
