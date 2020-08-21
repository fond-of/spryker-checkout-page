<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps\AddressStep;

use Codeception\Test\Unit;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientBridge;
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
    public const CUSTOMER_E_MAIL = 'test@test.dev';

    /**
     * @return void
     */
    public function testUpdateQuoteShipmentTransferFromQuoteWithBillingSameAsShippingFalse()
    {
        $customerServiceMock = $this->createCustomerServiceMock(['getUniqueAddressKey']);
        $customerServiceMock->expects($this->exactly(0))->method('getUniqueAddressKey')->willReturn('');

        $customerClientMock = $this->createCustomerClientMock(['getCustomer']);
        $customerClientMock->method('getCustomer')->willReturn(null);

        $requestMock = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();

        $customerTransfer = new CustomerTransfer();
        $quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)->setMethods(['getShippingAddress', 'getCustomer', 'getBillingSameAsShipping'])->getMock();
        $quoteTransferMock->expects($this->atLeastOnce())->method('getShippingAddress')->willReturn(new AddressTransfer());
        $quoteTransferMock->expects($this->atLeastOnce())->method('getCustomer')->willReturn($customerTransfer);
        $quoteTransferMock->expects($this->atLeastOnce())->method('getBillingSameAsShipping')->willReturn(false);

        $shipmentTransferMock = $this->getMockBuilder(ShipmentTransfer::class)->setMethods(['getShipment'])->getMock();
        $shipmentTransferMock->expects($this->never())->method('getShipment');

        $executor = new ShippingAddressStepExecutor($customerServiceMock, $customerClientMock, []);
        $executor->execute($requestMock, $quoteTransferMock);
    }

    /**
     * @return void
     */
    public function testUpdateQuoteShipmentTransferFromClientWithBillingSameAsShippingFalse()
    {
        $customerServiceMock = $this->createCustomerServiceMock(['getUniqueAddressKey']);
        $customerServiceMock->expects($this->exactly(0))->method('getUniqueAddressKey')->willReturn('');

        $customerTransfer = new CustomerTransfer();
        $customerClientMock = $this->createCustomerClientMock(['getCustomer']);
        $customerClientMock->method('getCustomer')->willReturn($customerTransfer);

        $requestMock = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();

        $quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)->setMethods(['getShippingAddress', 'getCustomer', 'getBillingSameAsShipping'])->getMock();
        $quoteTransferMock->expects($this->atLeastOnce())->method('getShippingAddress')->willReturn(new AddressTransfer());
        $quoteTransferMock->expects($this->never())->method('getCustomer');
        $quoteTransferMock->expects($this->atLeastOnce())->method('getBillingSameAsShipping')->willReturn(false);

        $shipmentTransferMock = $this->getMockBuilder(ShipmentTransfer::class)->setMethods(['getShipment'])->getMock();
        $shipmentTransferMock->expects($this->never())->method('getShipment');

        $executor = new ShippingAddressStepExecutor($customerServiceMock, $customerClientMock, []);
        $executor->execute($requestMock, $quoteTransferMock);
    }

    /**
     * @param array $methods
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createCustomerServiceMock(array $methods)
    {
        return $this->getMockBuilder(CheckoutPageToCustomerServiceBridge::class)->disableOriginalConstructor()->setMethods($methods)->getMock();
    }

    /**
     * @param array $methods
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createCustomerClientMock(array $methods)
    {
        return $this->getMockBuilder(CheckoutPageToCustomerClientBridge::class)->disableOriginalConstructor()->setMethods($methods)->getMock();
    }
}
