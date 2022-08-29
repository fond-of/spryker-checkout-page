<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps\AddressStep;

use Codeception\Test\Unit;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientBridge;
use FondOfSpryker\Yves\CheckoutPage\Resetter\OrderReferenceResetterInterface;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use SprykerShop\Yves\CheckoutPage\Dependency\Service\CheckoutPageToCustomerServiceBridge;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auto-generated group annotations
 *
 * @group FondOfSpryker
 * @group Yves
 * @group CheckoutPage
 * @group Process
 * @group Steps
 * @group AddressStep
 * @group ShippingAddressStepExecutorTest
 * Add your own group annotations below this line
 */
class ShippingAddressStepExecutorTest extends Unit
{
    /**
     * @var string
     */
    public const CUSTOMER_E_MAIL = 'test@test.dev';

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Resetter\OrderReferenceResetterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $orderReferenceResetterMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->orderReferenceResetterMock = $this->getMockBuilder(OrderReferenceResetterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return void
     */
    public function testUpdateQuoteShipmentTransferFromQuoteWithBillingSameAsShippingFalse()
    {
        $customerServiceMock = $this->getMockBuilder(CheckoutPageToCustomerServiceBridge::class)->disableOriginalConstructor()->setMethods(['getUniqueAddressKey'])->getMock();
        $customerServiceMock->expects($this->exactly(0))->method('getUniqueAddressKey')->willReturn('');

        $customerClientMock = $this->getMockBuilder(CheckoutPageToCustomerClientBridge::class)->disableOriginalConstructor()->setMethods(['getCustomer'])->getMock();
        $customerClientMock->method('getCustomer')->willReturn(null);

        $requestMock = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();

        $customerTransfer = new CustomerTransfer();
        $quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)->setMethods(['getShippingAddress', 'getCustomer', 'getBillingSameAsShipping'])->getMock();
        $quoteTransferMock->expects($this->atLeastOnce())->method('getShippingAddress')->willReturn(new AddressTransfer());
        $quoteTransferMock->expects($this->atLeastOnce())->method('getCustomer')->willReturn($customerTransfer);
        $quoteTransferMock->expects($this->atLeastOnce())->method('getBillingSameAsShipping')->willReturn(false);

        $shipmentTransferMock = $this->getMockBuilder(ShipmentTransfer::class)->setMethods(['getShipment'])->getMock();
        $shipmentTransferMock->expects($this->never())->method('getShipment');

        $this->orderReferenceResetterMock->expects($this->once())
            ->method('reset')
            ->with($quoteTransferMock)
            ->willReturn($quoteTransferMock);

        $executor = new ShippingAddressStepExecutor(
            $customerServiceMock,
            $customerClientMock,
            [],
            $this->orderReferenceResetterMock
        );
        $executor->execute($requestMock, $quoteTransferMock);
    }

    /**
     * @return void
     */
    public function testUpdateQuoteShipmentTransferFromClientWithBillingSameAsShippingFalse()
    {
        $customerServiceMock = $this->getMockBuilder(CheckoutPageToCustomerServiceBridge::class)->disableOriginalConstructor()->setMethods(['getUniqueAddressKey'])->getMock();
        $customerServiceMock->expects($this->exactly(0))->method('getUniqueAddressKey')->willReturn('');

        $customerTransfer = new CustomerTransfer();
        $customerClientMock = $this->getMockBuilder(CheckoutPageToCustomerClientBridge::class)->disableOriginalConstructor()->setMethods(['getCustomer'])->getMock();
        $customerClientMock->method('getCustomer')->willReturn($customerTransfer);

        $requestMock = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();

        $quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)->setMethods(['getShippingAddress', 'getCustomer', 'getBillingSameAsShipping'])->getMock();
        $quoteTransferMock->expects($this->atLeastOnce())->method('getShippingAddress')->willReturn(new AddressTransfer());
        $quoteTransferMock->expects($this->never())->method('getCustomer');
        $quoteTransferMock->expects($this->atLeastOnce())->method('getBillingSameAsShipping')->willReturn(false);

        $shipmentTransferMock = $this->getMockBuilder(ShipmentTransfer::class)->setMethods(['getShipment'])->getMock();
        $shipmentTransferMock->expects($this->never())->method('getShipment');

        $this->orderReferenceResetterMock->expects($this->once())
            ->method('reset')
            ->with($quoteTransferMock)
            ->willReturn($quoteTransferMock);

        $executor = new ShippingAddressStepExecutor(
            $customerServiceMock,
            $customerClientMock,
            [],
            $this->orderReferenceResetterMock
        );
        $executor->execute($requestMock, $quoteTransferMock);
    }
}
