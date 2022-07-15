<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;
use SprykerShop\Yves\CheckoutPage\Process\Steps\PaymentStep as SprykerPaymentStep;
use Symfony\Component\HttpFoundation\Request;

class PaymentStep extends SprykerPaymentStep
{
    use LoggerTrait;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer|\Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function execute(Request $request, AbstractTransfer $quoteTransfer)
    {
        if ($quoteTransfer->getCheckoutConfirmed() === true) {
            $this->resetOrderReference($quoteTransfer);
        }

        return parent::execute($request, $quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function resetOrderReference(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $this->getLogger()->notice(
            sprintf(
                '[ORDER RESET] Order with reference %s has been reseted.',
                $quoteTransfer->getOrderReference()
            )
        );
        $quoteTransfer->setCheckoutConfirmed(false);
        $quoteTransfer->setOrderReference(null);
        $quoteTransfer->setIdSalesOrder(null);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string|null
     */
    protected function getPaymentSelectionWithFallback(QuoteTransfer $quoteTransfer): ?string
    {
        //ToDo 2022 Spryker Upgrade - since the payment selection is an object and a string is expected I had to override it.
        if (method_exists($quoteTransfer, 'getPayment')){
            $paymentTransfer = $quoteTransfer->getPayment();

            if ($paymentTransfer) {
                $selection = $paymentTransfer->getPaymentSelection();
                if (is_object($selection) && method_exists($selection, 'getName')){
                    $paymentTransfer->setPaymentSelection($selection->getName());
                    $quoteTransfer->setPayment($paymentTransfer);
                }
            }
        }

        return parent::getPaymentSelectionWithFallback($quoteTransfer);
    }
}
