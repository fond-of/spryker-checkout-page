<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Codeception\Test\Unit;
use FondOfSpryker\Yves\CheckoutPage\Resetter\OrderReferenceResetterInterface;
use Generated\Shared\Transfer\QuoteTransfer;
use Psr\Log\LoggerInterface;
use Spryker\Shared\Log\Config\LoggerConfigInterface;
use Spryker\Yves\Messenger\FlashMessenger\FlashMessenger;
use Spryker\Yves\Messenger\FlashMessenger\FlashMessengerInterface;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientBridge;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientBridge;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientInterface;
use SprykerShop\Yves\CheckoutPage\Extractor\PaymentMethodKeyExtractor;
use SprykerShop\Yves\CheckoutPage\Extractor\PaymentMethodKeyExtractorInterface;
use Symfony\Component\HttpFoundation\Request;

class PaymentStepTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteTransfer
     */
    private $quoteTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\HttpFoundation\Request
     */
    private $requestMock;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $loggerMock;

    /**
     * @var \SprykerShop\Yves\CheckoutPage\Extractor\PaymentMethodKeyExtractor|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $paymentMethodKeyExtractorMock;

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Process\Steps\PaymentStep
     */
    private $paymentStep;

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Resetter\OrderReferenceResetterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $orderReferenceResetterMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->onlyMethods(['setCheckoutConfirmed', 'setOrderReference', 'setIdSalesOrder', 'getCheckoutConfirmed'])
            ->getMock();
        $this->requestMock = $this->getMockBuilder(Request::class)->getMock();
        $paymentClientMock = $this->getMockBuilder(CheckoutPageToPaymentClientBridge::class)
            ->disableOriginalConstructor()
            ->getMock();
        $paymentMethodHandlerMocker = $this->getMockBuilder(StepHandlerPluginCollection::class)->getMock();
        $flashMessagenerMock = $this->getMockBuilder(FlashMessenger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $calculationClientMock = $this->getMockBuilder(CheckoutPageToCalculationClientBridge::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->paymentMethodKeyExtractorMock = $this->getMockBuilder(PaymentMethodKeyExtractor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderReferenceResetterMock = $this->getMockBuilder(OrderReferenceResetterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentStep = new class (
            $paymentClientMock,
            $paymentMethodHandlerMocker,
            '',
            '',
            $flashMessagenerMock,
            $calculationClientMock,
            [],
            $this->paymentMethodKeyExtractorMock,
            $this->loggerMock,
            $this->orderReferenceResetterMock
) extends PaymentStep {
            /**
             * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientInterface $paymentClient
             * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection $paymentPlugins
             * @param string $stepRoute
             * @param string|null $escapeRoute
             * @param \Spryker\Yves\Messenger\FlashMessenger\FlashMessengerInterface $flashMessenger
             * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface $calculationClient
             * @param array $checkoutPaymentStepEnterPreCheckPlugins
             * @param \SprykerShop\Yves\CheckoutPage\Extractor\PaymentMethodKeyExtractorInterface $paymentMethodKeyExtractor
             * @param \Psr\Log\LoggerInterface $loggerMock
             * @param \FondOfSpryker\Yves\CheckoutPage\Resetter\OrderReferenceResetterInterface $orderReferenceResetterMock
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
                LoggerInterface $loggerMock,
                OrderReferenceResetterInterface $orderReferenceResetterMock
            ) {
                parent::__construct(
                    $paymentClient,
                    $paymentPlugins,
                    $stepRoute,
                    $escapeRoute,
                    $flashMessenger,
                    $calculationClient,
                    $checkoutPaymentStepEnterPreCheckPlugins,
                    $paymentMethodKeyExtractor,
                    $orderReferenceResetterMock,
                );

                $this->loggerMock = $loggerMock;
            }

            /**
             * @param \Spryker\Shared\Log\Config\LoggerConfigInterface|null $loggerConfig
             *
             * @return \Psr\Log\LoggerInterface
             */
            protected function getLogger(?LoggerConfigInterface $loggerConfig = null): LoggerInterface
            {
                return $this->loggerMock;
            }
        };
    }

    /**
     * @return void
     */
    public function testExecute(): void
    {
        $this->quoteTransferMock->method('getCheckoutConfirmed')->willReturn(true);
        $this->orderReferenceResetterMock->expects($this->once())->method('reset')->with($this->quoteTransferMock);
        $this->paymentStep->execute($this->requestMock, $this->quoteTransferMock);
    }
}
