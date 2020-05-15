<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Step\StepWithBreadcrumbInterface;

class ShippingAddressStep extends AddressStep implements StepWithBreadcrumbInterface
{
    public const BREADCRUMB_ITEM_TITLE = 'checkout.step.shipping-address.title';

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
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $quoteTransfer
     *
     * @return bool|void
     */
    public function postCondition(AbstractTransfer $quoteTransfer): bool
    {
        /** @var \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer */

        foreach ($quoteTransfer->getItems() as $item) {
            $shipment = $item->getShipment();
            if ($shipment === null || $shipment->getShippingAddress() === null) {
                return false;
            }

            if ($quoteTransfer->getBillingSameAsShipping() === false && $this->isAddressEmpty($shipment->getShippingAddress()) === true) {
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
        return static::BREADCRUMB_ITEM_TITLE;
    }
}
