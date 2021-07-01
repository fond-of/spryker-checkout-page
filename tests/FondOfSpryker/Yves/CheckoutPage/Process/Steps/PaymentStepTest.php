<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\QuoteTransfer;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Spryker\Shared\Log\Config\LoggerConfigInterface;
use Spryker\Shared\Log\LoggerFactory;
use Spryker\Yves\Messenger\FlashMessenger\FlashMessenger;
use Spryker\Yves\Messenger\FlashMessenger\FlashMessengerInterface;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientBridge;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientBridge;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientInterface;
use Symfony\Component\HttpFoundation\Request;

class PaymentStepTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $quoteTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $requestMock;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $loggerMock;

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Process\Steps\PaymentStep
     */
    private $paymentStep;

    protected function _before(): void
    {
        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
                                    ->onlyMethods(['setCheckoutConfirmed', 'setOrderReference', 'setIdSalesOrder', 'getCheckoutConfirmed'])
                                    ->getMock();
        $this->requestMock = $this->getMockBuilder(Request::class)->getMock();
        $this->loggerMock = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
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

        $this->paymentStep = new class(
            $paymentClientMock,
            $paymentMethodHandlerMocker,
            '',
            '',
            $flashMessagenerMock,
            $calculationClientMock,
            [],
            $this->loggerMock) extends PaymentStep {

            /**
             * @var \Psr\Log\LoggerInterface
             */
            protected $loggerMock;

            /**
             *  constructor.
             *
             * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientInterface $paymentClient
             * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection $paymentPlugins
             * @param $stepRoute
             * @param $escapeRoute
             * @param \Spryker\Yves\Messenger\FlashMessenger\FlashMessengerInterface $flashMessenger
             * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface $calculationClient
             * @param array $checkoutPaymentStepEnterPreCheckPlugins
             * @param \Psr\Log\LoggerInterface $loggerMock
             */
            public function __construct(CheckoutPageToPaymentClientInterface $paymentClient, StepHandlerPluginCollection $paymentPlugins, $stepRoute, $escapeRoute, FlashMessengerInterface $flashMessenger, CheckoutPageToCalculationClientInterface $calculationClient, array $checkoutPaymentStepEnterPreCheckPlugins, LoggerInterface $loggerMock)
            {
                parent::__construct($paymentClient, $paymentPlugins, $stepRoute, $escapeRoute, $flashMessenger, $calculationClient, $checkoutPaymentStepEnterPreCheckPlugins);
                $this->loggerMock = $loggerMock;
            }

            /**
             * @param \Spryker\Shared\Log\Config\LoggerConfigInterface|null $loggerConfig
             *
             * @return \Psr\Log\LoggerInterface|null
             */
            protected function getLogger(?LoggerConfigInterface $loggerConfig = null)
            {
                return $this->loggerMock;
            }
        };
    }

    /**
     * @return void
     */
    public function testExecuteCheckoutConfirmed(): void
    {
        $this->quoteTransferMock->method('getCheckoutConfirmed')->willReturn(true);
        $this->quoteTransferMock->expects($this->once())->method('setCheckoutConfirmed')->with(false);
        $this->quoteTransferMock->expects($this->once())->method('setOrderReference')->with(null);
        $this->quoteTransferMock->expects($this->once())->method('setIdSalesOrder')->with(null);
        $this->paymentStep->execute($this->requestMock, $this->quoteTransferMock);
    }

    /**
     * @return void
     */
    public function testExecuteCheckoutNotConfirmed(): void
    {
        $this->quoteTransferMock->method('getCheckoutConfirmed')->willReturn(false);
        $this->quoteTransferMock->expects($this->never())->method('setCheckoutConfirmed');
        $this->quoteTransferMock->expects($this->never())->method('setOrderReference');
        $this->quoteTransferMock->expects($this->never())->method('setIdSalesOrder');
        $this->paymentStep->execute($this->requestMock, $this->quoteTransferMock);
    }
}
