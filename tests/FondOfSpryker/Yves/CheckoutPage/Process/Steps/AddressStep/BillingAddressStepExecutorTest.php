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
    /**
     * @var string
     */
    public const CUSTOMER_E_MAIL = 'test@test.dev';

    /**
     * @return void
     */
    public function testUpdateCustomerDataFromBillingAddressFromQuote()
    {
        $customerServiceMock = $this->getMockBuilder(CheckoutPageToCustomerServiceBridge::class)->disableOriginalConstructor()->setMethods(['getUniqueAddressKey'])->getMock();
        $customerServiceMock->expects($this->exactly(0))->method('getUniqueAddressKey')->willReturn('');

        $customerClientMock = $this->getMockBuilder(CheckoutPageToCustomerClientBridge::class)->disableOriginalConstructor()->setMethods(['getCustomer'])->getMock();
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
        $customerServiceMock = $this->getMockBuilder(CheckoutPageToCustomerServiceBridge::class)->disableOriginalConstructor()->setMethods(['getUniqueAddressKey'])->getMock();
        $customerServiceMock->expects($this->exactly(0))->method('getUniqueAddressKey')->willReturn('');

        $customerTransfer = new CustomerTransfer();
        $customerClientMock = $this->getMockBuilder(CheckoutPageToCustomerClientBridge::class)->disableOriginalConstructor()->setMethods(['getCustomer'])->getMock();
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
}
