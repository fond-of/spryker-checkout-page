<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps\AddressStep;

use Codeception\Test\Unit;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientBridge;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
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
 * @group BillingAddressStepExecutorTest
 * Add your own group annotations below this line
 */
class BillingAddressStepExecutorTest extends Unit
{
    public const CUSTOMER_E_MAIL = 'test@test.dev';

    /**
     * @return void
     */
    public function testUpdateCustomerDataFromBillingAddressFromQuote()
    {
        $customerServiceMock = $this->createCustomerServiceMock(['getUniqueAddressKey']);
        $customerServiceMock->expects($this->exactly(0))->method('getUniqueAddressKey')->willReturn(null);

        $customerClientMock = $this->createCustomerClientMock(['getCustomer']);
        $customerClientMock->method('getCustomer')->willReturn(null);

        $requestMock = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();

        $quoteTransfer = new QuoteTransfer();

        $customerTransfer = new CustomerTransfer();
        $quoteTransfer->setCustomer($customerTransfer);

        $billingAddressTransfer = new AddressTransfer();
        $billingAddressTransfer->setEmail(static::CUSTOMER_E_MAIL);

        $quoteTransfer->setBillingAddress($billingAddressTransfer);

        $executor = new BillingAddressStepExecutor($customerServiceMock, $customerClientMock, []);
        $quoteTransfer = $executor->execute($requestMock, $quoteTransfer);

        $this->assertSame($quoteTransfer->getCustomer()->getEmail(), static::CUSTOMER_E_MAIL);
    }

    /**
     * @return void
     */
    public function testUpdateCustomerDataFromBillingAddressFromClient()
    {
        $customerServiceMock = $this->createCustomerServiceMock(['getUniqueAddressKey']);
        $customerServiceMock->expects($this->exactly(0))->method('getUniqueAddressKey')->willReturn(null);

        $customerTransfer = new CustomerTransfer();
        $customerClientMock = $this->createCustomerClientMock(['getCustomer']);
        $customerClientMock->method('getCustomer')->willReturn($customerTransfer);

        $requestMock = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();

        $quoteTransfer = new QuoteTransfer();

        $billingAddressTransfer = new AddressTransfer();
        $billingAddressTransfer->setEmail(static::CUSTOMER_E_MAIL);

        $quoteTransfer->setBillingAddress($billingAddressTransfer);

        $executor = new BillingAddressStepExecutor($customerServiceMock, $customerClientMock, []);
        $quoteTransfer = $executor->execute($requestMock, $quoteTransfer);

        $this->assertSame($quoteTransfer->getCustomer()->getEmail(), static::CUSTOMER_E_MAIL);
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
