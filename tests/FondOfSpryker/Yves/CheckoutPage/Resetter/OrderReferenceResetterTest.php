<?php

namespace FondOfSpryker\Yves\CheckoutPage\Resetter;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\QuoteTransfer;
use Psr\Log\LoggerInterface;

class OrderReferenceResetterTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Log\LoggerInterface
     */
    protected $loggerMock;

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Resetter\OrderReferenceResetter
     */
    protected $orderReferenceResetter;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderReferenceResetter = new OrderReferenceResetter($this->loggerMock);
    }

    /**
     * @return void
     */
    public function testResetCheckoutConfirmed(): void
    {
        $this->quoteTransferMock->expects($this->once())->method('getCheckoutConfirmed')->willReturn(true);
        $this->loggerMock->expects($this->once())->method('notice');
        $this->quoteTransferMock->expects($this->once())->method('setCheckoutConfirmed')->with(false)->willReturnSelf();
        $this->quoteTransferMock->expects($this->once())->method('setIdSalesOrder')->with(null)->willReturnSelf();
        $this->quoteTransferMock->expects($this->once())->method('setOrderReference')->with(null)->willReturnSelf();

        $this->orderReferenceResetter->reset($this->quoteTransferMock);
    }

    /**
     * @return void
     */
    public function testResetCheckoutNotConfirmed(): void
    {
        $this->quoteTransferMock->expects($this->once())->method('getCheckoutConfirmed')->willReturn(false);
        $this->loggerMock->expects($this->never())->method('notice');
        $this->quoteTransferMock->expects($this->never())->method('setCheckoutConfirmed')->with(false)->willReturnSelf();
        $this->quoteTransferMock->expects($this->never())->method('setIdSalesOrder')->with(null)->willReturnSelf();
        $this->quoteTransferMock->expects($this->never())->method('setOrderReference')->with(null)->willReturnSelf();

        $this->orderReferenceResetter->reset($this->quoteTransferMock);
    }
}
