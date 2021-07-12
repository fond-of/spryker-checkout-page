<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerShop\Yves\CheckoutPage\CheckoutPageConfig;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface;
use SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\PostConditionCheckerInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\StepExecutorInterface;

class ShippingAddressStep extends AddressStep
{
    public const BREADCRUMB_ITEM_TITLE = 'checkout.step.shipping-address.title';
    /**
     * @var \SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface
     */
    protected $giftCardItemsChecker;

    public function __construct(
        CheckoutPageToCustomerClientInterface $customerClient,
        CheckoutPageToCalculationClientInterface $calculationClient,
        CheckoutPageToCountryInterface $countryClient,
        $stepRoute,
        $escapeRoute,
        StepExecutorInterface $stepExecutor,
        PostConditionCheckerInterface $postConditionChecker,
        CheckoutPageConfig $checkoutPageConfig,
        array $checkoutAddressStepEnterPreCheckPlugins,
        GiftCardItemsCheckerInterface $giftCardItemsChecker
    ) {
        parent::__construct(
            $customerClient,
            $calculationClient,
            $countryClient,
            $stepRoute,
            $escapeRoute,
            $stepExecutor,
            $postConditionChecker,
            $checkoutPageConfig,
            $checkoutAddressStepEnterPreCheckPlugins
        );

        $this->giftCardItemsChecker = $giftCardItemsChecker;
    }


    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $dataTransfer
     *
     * @return bool
     */
    public function requireInput(AbstractTransfer $dataTransfer): bool
    {
        /** @var \Generated\Shared\Transfer\QuoteTransfer $dataTransfer */
        return !$this->giftCardItemsChecker->hasOnlyGiftCardItems($dataTransfer->getItems())
            || !$dataTransfer->getBillingSameAsShipping();
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $quoteTransfer
     *
     * @return bool|void
     */
    public function postCondition(AbstractTransfer $quoteTransfer): bool
    {
        /** @var \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer */

        foreach ($quoteTransfer->getItems() as $item) {
            $shipment = $item->getShipment();
            if ($shipment === null || $shipment->getShippingAddress() === null) {
                return false;
            }

            if ($quoteTransfer->getBillingSameAsShipping() === false && $this->isAddressEmpty($shipment->getShippingAddress()) === true) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getBreadcrumbItemTitle()
    {
        return static::BREADCRUMB_ITEM_TITLE;
    }
}
