<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Codeception\Test\Unit;
use FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\AddressStep\BillingAddressStepExecutor;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientBridge;
use SprykerShop\Yves\CheckoutPage\Process\Steps\AddressStep\PostConditionChecker;

/**
 * Auto-generated group annotations
 *
 * @group FondOfSpryker
 * @group Yves
 * @group CheckoutPage
 * @group Process
 * @group Steps
 * @group BillingAddressStepTest
 * Add your own group annotations below this line
 */
class BillingAddressStepTest extends Unit
{
    /**
     * @return void
     */
    public function testPreCondition(): void
    {
        $dataTransferMock = $this->createMock(AbstractTransfer::class);
        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new BillingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            []
        );
        $this->assertTrue($step->preCondition($dataTransferMock));
    }

    /**
     * @return void
     */
    public function testPostConditionBillingAddressIsNullWillReturnFalse(): void
    {
        $dataTransferMock = $this->createMock(QuoteTransfer::class);
        $dataTransferMock->method('getBillingAddress')->willReturn(null);

        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new BillingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            []
        );
        $this->assertFalse($step->postCondition($dataTransferMock));
    }

    /**
     * @return void
     */
    public function testPostConditionBillingAddressIsEmptyWillReturnFalse(): void
    {
        $dataTransferMock = $this->createMock(QuoteTransfer::class);
        $addressTransferMock = $this->createMock(AddressTransfer::class);
        $addressTransferMock->method('getFirstName')->willReturn(null);
        $addressTransferMock->method('getLastName')->willReturn(null);
        $dataTransferMock->method('getBillingAddress')->willReturn($addressTransferMock);

        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new BillingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            []
        );
        $this->assertFalse($step->postCondition($dataTransferMock));
    }

    /**
     * @return void
     */
    public function testPostConditionBillingAddressHasNoIdButNamesWillReturnTrue(): void
    {
        $dataTransferMock = $this->createMock(QuoteTransfer::class);
        $addressTransferMock = $this->createMock(AddressTransfer::class);
        $addressTransferMock->method('getFirstName')->willReturn('Hans');
        $addressTransferMock->method('getLastName')->willReturn('Wurst');
        $addressTransferMock->method('getIdCustomerAddress')->willReturn(null);
        $dataTransferMock->method('getBillingAddress')->willReturn($addressTransferMock);

        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new BillingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            []
        );
        $this->assertTrue($step->postCondition($dataTransferMock));
    }

    /**
     * @return void
     */
    public function testPostConditionBillingAddressHasNoNamesButGotAnIdWillReturnTrue(): void
    {
        $dataTransferMock = $this->createMock(QuoteTransfer::class);
        $addressTransferMock = $this->createMock(AddressTransfer::class);
        $addressTransferMock->method('getFirstName')->willReturn('');
        $addressTransferMock->method('getLastName')->willReturn('');
        $addressTransferMock->method('getIdCustomerAddress')->willReturn(0);
        $dataTransferMock->method('getBillingAddress')->willReturn($addressTransferMock);

        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new BillingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            []
        );
        $this->assertTrue($step->postCondition($dataTransferMock));
    }

    /**
     * @return void
     */
    public function testGetBreadcrumbItemTitle(): void
    {
        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new BillingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            []
        );

        $this->assertSame($step->getBreadcrumbItemTitle(), BillingAddressStep::BREADCRUMB_ITEM_TITLE);
    }
}
