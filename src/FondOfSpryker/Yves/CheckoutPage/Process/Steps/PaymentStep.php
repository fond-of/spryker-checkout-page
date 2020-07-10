<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerShop\Yves\CheckoutPage\Process\Steps\PaymentStep as SprykerPaymentStep;

class PaymentStep extends SprykerPaymentStep
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string|null
     */
    protected function getPaymentSelectionWithFallback(QuoteTransfer $quoteTransfer): ?string
    {
        if ($quoteTransfer->getTotals() && $quoteTransfer->getTotals()->getPriceToPay() === 0) {
            return CheckoutPageConfig::PAYMENT_METHOD_NAME_NO_PAYMENT;
        }

        $paymentTransfer = $quoteTransfer->getPayment();

        if ($paymentTransfer) {
            return $paymentTransfer->getPaymentSelection();
        }

        return null;
    }
}
