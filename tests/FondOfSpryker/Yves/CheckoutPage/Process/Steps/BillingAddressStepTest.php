<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use ArrayObject;
use Codeception\Test\Unit;
use FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\AddressStep\BillingAddressStepExecutor;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientBridge;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientBridge;
use SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface;
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
        $dataTransferMock = $this->createMock(QuoteTransfer::class);
        $dataTransferMock->method('getItems')->willReturn(
            new ArrayObject([$this->createMock(ItemTransfer::class)]),
        );

        $customerClientMock = $this->createMock(CheckoutPageToCustomerClientBridge::class);
        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);
        $giftCardItemCheckerMock = $this->createMock(GiftCardItemsCheckerInterface::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new BillingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            [],
            $giftCardItemCheckerMock,
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
        $giftCardItemCheckerMock = $this->createMock(GiftCardItemsCheckerInterface::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new BillingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            [],
            $giftCardItemCheckerMock,
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
        $giftCardItemCheckerMock = $this->createMock(GiftCardItemsCheckerInterface::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new BillingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            [],
            $giftCardItemCheckerMock,
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
        $giftCardItemCheckerMock = $this->createMock(GiftCardItemsCheckerInterface::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new BillingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            [],
            $giftCardItemCheckerMock,
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
        $addressTransferMock->method('getAddress1')->willReturn('');
        $dataTransferMock->method('getBillingAddress')->willReturn($addressTransferMock);

        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);
        $giftCardItemCheckerMock = $this->createMock(GiftCardItemsCheckerInterface::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new BillingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            [],
            $giftCardItemCheckerMock,
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
        $giftCardItemCheckerMock = $this->createMock(GiftCardItemsCheckerInterface::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new BillingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            [],
            $giftCardItemCheckerMock,
        );

        $this->assertSame($step->getBreadcrumbItemTitle(), BillingAddressStep::BREADCRUMB_ITEM_TITLE);
    }
}
