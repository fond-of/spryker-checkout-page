<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use ArrayObject;
use Codeception\Test\Unit;
use FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig;
use FondOfSpryker\Yves\CheckoutPage\Process\Steps\AddressStep\BillingAddressStepExecutor;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientBridge;
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
 * @group ShippingAddressStepTest
 * Add your own group annotations below this line
 */
class ShippingAddressStepTest extends Unit
{
    /**
     * @return void
     */
    public function testRequireInputWillReturnFalse(): void
    {
        $dataTransferMock = $this->createMock(QuoteTransfer::class);
        $dataTransferMock->method('getBillingSameAsShipping')->willReturn(true);

        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);
        $giftCardItemCheckerMock = $this->createMock(GiftCardItemsCheckerInterface::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new ShippingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            [],
            $giftCardItemCheckerMock,
        );
        $this->assertFalse($step->requireInput($dataTransferMock));
    }

    /**
     * @return void
     */
    public function testRequireInputWillReturnTrue(): void
    {
        $dataTransferMock = $this->createMock(QuoteTransfer::class);
        $dataTransferMock->method('getBillingSameAsShipping')->willReturn(false);
        $dataTransferMock->method('getItems')->willReturn(
            new ArrayObject([$this->createMock(ItemTransfer::class)]),
        );

        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);
        $giftCardItemCheckerMock = $this->createMock(GiftCardItemsCheckerInterface::class);
        $giftCardItemCheckerMock->method('hasOnlyGiftCardItems')->willReturn(false);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new ShippingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            [],
            $giftCardItemCheckerMock,
        );
        $this->assertTrue($step->requireInput($dataTransferMock));
    }

    /**
     * @return void
     */
    public function testPostConditionQuoteWithNoItemWillReturnTrue(): void
    {
        $dataTransferMock = $this->createMock(QuoteTransfer::class);
        $dataTransferMock->method('getItems')->willReturn([]);

        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);
        $giftCardItemCheckerMock = $this->createMock(GiftCardItemsCheckerInterface::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new ShippingAddressStep(
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
    public function testPostConditionShippingAddressOnOneItemWillReturnTrue(): void
    {
        $dataTransferMock = $this->createMock(QuoteTransfer::class);
        $addressTransferMock = $this->createMock(AddressTransfer::class);
        $addressTransferMock->method('getFirstName')->willReturn('Hans');
        $addressTransferMock->method('getLastName')->willReturn('Wurst');
        $shipmentTransferMock = $this->createMock(ShipmentTransfer::class);
        $shipmentTransferMock->method('getShippingAddress')->willReturn($addressTransferMock);
        $itemTransferMock = $this->createMock(ItemTransfer::class);
        $itemTransferMock->method('getShipment')->willReturn($shipmentTransferMock);

        $dataTransferMock->method('getItems')->willReturn([$itemTransferMock]);

        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);
        $giftCardItemCheckerMock = $this->createMock(GiftCardItemsCheckerInterface::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new ShippingAddressStep(
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
    public function testPostConditionShippingAddressOnTwoItemWillReturnFalse(): void
    {
        $dataTransferMock = $this->createMock(QuoteTransfer::class);
        $addressTransferMock = $this->createMock(AddressTransfer::class);
        $addressTransferMock->method('getFirstName')->willReturn('Hans');
        $addressTransferMock->method('getLastName')->willReturn('Wurst');
        $shipmentTransferMock = $this->createMock(ShipmentTransfer::class);
        $shipmentTransferMock2 = $this->createMock(ShipmentTransfer::class);
        $shipmentTransferMock->method('getShippingAddress')->willReturn($addressTransferMock);
        $shipmentTransferMock2->method('getShippingAddress')->willReturn(null);
        $itemTransferMock = $this->createMock(ItemTransfer::class);
        $itemTransferMock2 = $this->createMock(ItemTransfer::class);
        $itemTransferMock->method('getShipment')->willReturn($shipmentTransferMock);
        $itemTransferMock2->method('getShipment')->willReturn($shipmentTransferMock2);

        $dataTransferMock->method('getItems')->willReturn([$itemTransferMock, $itemTransferMock2]);

        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);
        $giftCardItemCheckerMock = $this->createMock(GiftCardItemsCheckerInterface::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new ShippingAddressStep(
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
    public function testGetBreadcrumbItemTitle(): void
    {
        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientBridge::class);
        $stepExecutorMock = $this->createMock(BillingAddressStepExecutor::class);
        $postConditionCheckerMock = $this->createMock(PostConditionChecker::class);
        $checkoutPageConfigMock = $this->createMock(CheckoutPageConfig::class);
        $giftCardItemCheckerMock = $this->createMock(GiftCardItemsCheckerInterface::class);

        $stepRoute = '';
        $escapeRoute = '';

        $step = new ShippingAddressStep(
            $calculationClientMock,
            $stepRoute,
            $escapeRoute,
            $stepExecutorMock,
            $postConditionCheckerMock,
            $checkoutPageConfigMock,
            [],
            $giftCardItemCheckerMock,
        );

        $this->assertSame($step->getBreadcrumbItemTitle(), ShippingAddressStep::BREADCRUMB_ITEM_TITLE);
    }
}
