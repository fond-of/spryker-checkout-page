<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use FondOfSpryker\Yves\CheckoutPage\Resetter\OrderReferenceResetterInterface;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\Messenger\FlashMessenger\FlashMessengerInterface;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\PaymentStep as SprykerPaymentStep;
use Symfony\Component\HttpFoundation\Request;

class PaymentStep extends SprykerPaymentStep
{
    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Resetter\OrderReferenceResetterInterface
     */
    protected $orderReferenceResetter;

    /**
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientInterface $paymentClient
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection $paymentPlugins
     * @param string $stepRoute
     * @param string|null $escapeRoute
     * @param \Spryker\Yves\Messenger\FlashMessenger\FlashMessengerInterface $flashMessenger
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface $calculationClient
     * @param array $checkoutPaymentStepEnterPreCheckPlugins
     * @param \FondOfSpryker\Yves\CheckoutPage\Resetter\OrderReferenceResetterInterface $orderReferenceResetter
     */
    public function __construct(
        CheckoutPageToPaymentClientInterface $paymentClient,
        StepHandlerPluginCollection $paymentPlugins,
        $stepRoute,
        $escapeRoute,
        FlashMessengerInterface $flashMessenger,
        CheckoutPageToCalculationClientInterface $calculationClient,
        array $checkoutPaymentStepEnterPreCheckPlugins,
        OrderReferenceResetterInterface $orderReferenceResetter
    ) {
        parent::__construct(
            $paymentClient,
            $paymentPlugins,
            $stepRoute,
            $escapeRoute,
            $flashMessenger,
            $calculationClient,
            $checkoutPaymentStepEnterPreCheckPlugins,
        );

        $this->orderReferenceResetter = $orderReferenceResetter;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer|\Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function execute(Request $request, AbstractTransfer $quoteTransfer)
    {
        $quoteTransfer = $this->orderReferenceResetter->reset($quoteTransfer);

        return parent::execute($request, $quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string|null
     */
    protected function getPaymentSelectionWithFallback(QuoteTransfer $quoteTransfer): ?string
    {
        //ToDo 2022 Spryker Upgrade - since the payment selection is an object and a string is expected I had to override it.
        if (method_exists($quoteTransfer, 'getPayment')) {
            $paymentTransfer = $quoteTransfer->getPayment();

            if ($paymentTransfer) {
                $selection = $paymentTransfer->getPaymentSelection();
                // @phpstan-ignore-next-line
                if (is_object($selection) && method_exists($selection, 'getName')) {
                    $paymentTransfer->setPaymentSelection($selection->getName());
                    $quoteTransfer->setPayment($paymentTransfer);
                }
            }
        }

        return parent::getPaymentSelectionWithFallback($quoteTransfer);
    }
}
