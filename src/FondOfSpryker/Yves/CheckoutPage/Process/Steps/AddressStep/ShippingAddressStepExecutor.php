<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps\AddressStep;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use SprykerShop\Yves\CheckoutPage\Process\Steps\AddressStep\AddressStepExecutor as SprykerAddressStepExecutor;
use Symfony\Component\HttpFoundation\Request;

class ShippingAddressStepExecutor extends SprykerAddressStepExecutor
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

        $quoteTransfer = $this->updateQuoteShipmentTransfer($quoteTransfer);
        $quoteTransfer = $this->hydrateShippingAddress($quoteTransfer, $customerTransfer);
        $quoteTransfer = $this->setQuoteShippingAddress($quoteTransfer, $customerTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function updateQuoteShipmentTransfer(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        if ($quoteTransfer->getBillingSameAsShipping() === true) {
            $billingAddressTransfer = $quoteTransfer->getBillingAddress();
            $shipmentTransfer = $quoteTransfer->getShipment();
            if ($shipmentTransfer === null) {
                $shipmentTransfer = new ShipmentTransfer();
            }
            $shipmentTransfer->setShippingAddress($billingAddressTransfer);
            $quoteTransfer->setShippingAddress($billingAddressTransfer);
            $quoteTransfer->setShipment($shipmentTransfer);
        }

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function hydrateShippingAddress(
        QuoteTransfer $quoteTransfer,
        CustomerTransfer $customerTransfer
    ): QuoteTransfer {
        if ($quoteTransfer->getBillingSameAsShipping()) {
            return $this->hydrateItemLevelShippingAddressesSameAsBillingAddress($quoteTransfer, $customerTransfer);
        }

        return $this->hydrateItemLevelShippingAddresses($quoteTransfer, $customerTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CustomerTransfer|null $customerTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function hydrateItemLevelShippingAddresses(
        QuoteTransfer $quoteTransfer,
        ?CustomerTransfer $customerTransfer
    ): QuoteTransfer {
        if ($quoteTransfer->getItems()->count() === 0) {
            return $quoteTransfer;
        }
        $shippingAddressTransfer = $quoteTransfer->getShippingAddress();
        $shipmentTransfer = $quoteTransfer->getShipment();
        $shipmentTransfer->setShippingAddress($shippingAddressTransfer);

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $itemTransfer->requireShipment();
            $itemTransfer->getShipment()->requireShippingAddress();

            $shipmentTransfer = $this->getShipmentWithUniqueShippingAddress(
                $shipmentTransfer,
                $customerTransfer
            );
            $itemTransfer->setShipment($shipmentTransfer);
        }

        return $this->setDefaultShippingAddress($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CustomerTransfer|null $customerTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function hydrateItemLevelShippingAddressesSameAsBillingAddress(
        QuoteTransfer $quoteTransfer,
        ?CustomerTransfer $customerTransfer
    ): QuoteTransfer {
        $billingAddressTransfer = $quoteTransfer->getBillingAddress();

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $shipmentTransfer = $itemTransfer->getShipment();
            if ($shipmentTransfer === null) {
                $shipmentTransfer = new ShipmentTransfer();
            }
            $shipmentTransfer->setShippingAddress($billingAddressTransfer);
            $itemTransfer->setShipment($shipmentTransfer);
        }

        return $this->hydrateItemLevelShippingAddresses($quoteTransfer, $customerTransfer);
    }
}
