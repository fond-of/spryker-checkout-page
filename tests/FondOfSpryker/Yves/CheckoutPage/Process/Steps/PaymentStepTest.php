<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Yves\Messenger\FlashMessenger\FlashMessenger;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientBridge;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientBridge;
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
     * @var \FondOfSpryker\Yves\CheckoutPage\Process\Steps\PaymentStep
     */
    private $paymentStep;

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

        $this->paymentStep = new PaymentStep(
            $paymentClientMock,
            $paymentMethodHandlerMocker,
            '',
            '',
            $flashMessagenerMock,
            $calculationClientMock,
            []
        );
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
