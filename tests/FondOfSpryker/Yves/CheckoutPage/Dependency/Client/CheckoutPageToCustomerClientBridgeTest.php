<?php

namespace FondOfSpryker\Yves\CheckoutPage\Dependency\Client;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\Customer\CustomerClient;

class CheckoutPageToCustomerClientBridgeTest extends Unit
{
    /**
     * @var \Spryker\Client\Customer\CustomerClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $customerClientMock;

    /**
     * @var \Generated\Shared\Transfer\CustomerTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $customerTransferMock;

    /**
     * @var \Generated\Shared\Transfer\AddressTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $addressTransferMock;

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface
     */
    protected $bridge;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->customerClientMock = $this->getMockBuilder(CustomerClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerTransferMock = $this->getMockBuilder(CustomerTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->addressTransferMock = $this->getMockBuilder(AddressTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->bridge = new CheckoutPageToCustomerClientBridge($this->customerClientMock);
    }

    /**
     * @return void
     */
    public function testMarkCustomerAsDirty(): void
    {
        $this->customerClientMock->expects(static::atLeastOnce())
            ->method('markCustomerAsDirty');

        $this->bridge->markCustomerAsDirty();
    }

    /**
     * @return void
     */
    public function testGetCustomer(): void
    {
        $this->customerClientMock->expects(static::atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerTransferMock);

        static::assertEquals($this->customerTransferMock, $this->bridge->getCustomer());
    }

    /**
     * @return void
     */
    public function testGetAddress(): void
    {
        $this->customerClientMock->expects(static::atLeastOnce())
            ->method('getAddress')
            ->with($this->addressTransferMock)
            ->willReturn($this->addressTransferMock);

        static::assertEquals($this->addressTransferMock, $this->bridge->getAddress($this->addressTransferMock));
    }

    /**
     * @return void
     */
    public function testFindCustomerById(): void
    {
        $this->customerClientMock->expects(static::atLeastOnce())
            ->method('getCustomerByEmail')
            ->with($this->customerTransferMock)
            ->willReturn($this->customerTransferMock);

        static::assertEquals($this->customerTransferMock, $this->bridge->getCustomerByEmail($this->customerTransferMock));
    }
}
