<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Generated\Shared\Transfer\QuoteTransfer;
use Psr\Log\LoggerInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\Messenger\FlashMessenger\FlashMessengerInterface;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientInterface;
use SprykerShop\Yves\CheckoutPage\Extractor\PaymentMethodKeyExtractorInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\PaymentStep as SprykerPaymentStep;
use Symfony\Component\HttpFoundation\Request;

class PaymentStep extends SprykerPaymentStep
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientInterface $paymentClient
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection $paymentPlugins
     * @param string $stepRoute
     * @param string|null $escapeRoute
     * @param \Spryker\Yves\Messenger\FlashMessenger\FlashMessengerInterface $flashMessenger
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface $calculationClient
     * @param \SprykerShop\Yves\CheckoutPageExtension\Dependency\Plugin\CheckoutPaymentStepEnterPreCheckPluginInterface[] $checkoutPaymentStepEnterPreCheckPlugins
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        CheckoutPageToPaymentClientInterface $paymentClient,
        StepHandlerPluginCollection $paymentPlugins,
        string $stepRoute,
        ?string $escapeRoute,
        FlashMessengerInterface $flashMessenger,
        CheckoutPageToCalculationClientInterface $calculationClient,
        array $checkoutPaymentStepEnterPreCheckPlugins,
        PaymentMethodKeyExtractorInterface $paymentMethodKeyExtractor,
        LoggerInterface $logger
    ) {
        parent::__construct(
            $paymentClient,
            $paymentPlugins,
            $stepRoute,
            $escapeRoute,
            $flashMessenger,
            $calculationClient,
            $checkoutPaymentStepEnterPreCheckPlugins,
            $paymentMethodKeyExtractor
        );

        $this->logger = $logger;
    }

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
        $this->logger->notice(
            sprintf(
                '[ORDER RESET] Order with reference %s has been reseted.',
                $quoteTransfer->getOrderReference(),
            ),
        );
        $quoteTransfer->setCheckoutConfirmed(false);
        $quoteTransfer->setOrderReference(null);
        $quoteTransfer->setIdSalesOrder(null);

        return $quoteTransfer;
    }
}
