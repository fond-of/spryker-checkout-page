<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerShop\Yves\CheckoutPage\Process\Steps\PlaceOrderStep as SprykerShopPlaceOrderStep;
use Symfony\Component\HttpFoundation\Request;

class PlaceOrderStep extends SprykerShopPlaceOrderStep
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function execute(Request $request, AbstractTransfer $quoteTransfer)
    {
        $quoteTransfer->setLocale((new LocaleTransfer())->setLocaleName($this->currentLocale));
        $checkoutResponseTransfer = $this->checkoutClient->placeOrder($quoteTransfer);

        if ($checkoutResponseTransfer->getIsExternalRedirect()) {
            $this->externalRedirectUrl = $checkoutResponseTransfer->getRedirectUrl();
        }

        if ($checkoutResponseTransfer->getSaveOrder() !== null) {
            $quoteTransfer->setOrderReference($checkoutResponseTransfer->getSaveOrder()->getOrderReference());
            $quoteTransfer->setIdSalesOrder($checkoutResponseTransfer->getSaveOrder()->getIdSalesOrder());
            $quoteTransfer->setIsOrderPlacedSuccessfully($checkoutResponseTransfer->getIsSuccess());
        }

        $this->setCheckoutErrorMessages($checkoutResponseTransfer);
        $this->checkoutResponseTransfer = $checkoutResponseTransfer;

        return $quoteTransfer;
    }
}
