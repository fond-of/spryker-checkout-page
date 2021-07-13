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

class BillingAddressStep extends AddressStep
{
    public const BREADCRUMB_ITEM_TITLE = 'checkout.step.billing-address.title';

    /**
     * @var \SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface
     */
    protected $giftCardItemsChecker;

    /**
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface $customerClient
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface $calculationClient
     * @param \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryInterface $countryClient
     * @param $stepRoute
     * @param $escapeRoute
     * @param \SprykerShop\Yves\CheckoutPage\Process\Steps\StepExecutorInterface $stepExecutor
     * @param \SprykerShop\Yves\CheckoutPage\Process\Steps\PostConditionCheckerInterface $postConditionChecker
     * @param \SprykerShop\Yves\CheckoutPage\CheckoutPageConfig $checkoutPageConfig
     * @param \SprykerShop\Yves\CheckoutPageExtension\Dependency\Plugin\CheckoutAddressStepEnterPreCheckPluginInterface[] $checkoutAddressStepEnterPreCheckPlugins
     * @param \SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface $giftCardItemsChecker
     */
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
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function postCondition(AbstractTransfer $quoteTransfer)
    {
        $billingAddress = $quoteTransfer->getBillingAddress();
        $isBillingSamesAsShipping = $quoteTransfer->getBillingSameAsShipping();
        $items = $quoteTransfer->getItems();

        if ($billingAddress === null || $this->isAddressEmpty($billingAddress)) {
            return false;
        }

        if ($isBillingSamesAsShipping === false && $this->giftCardItemsChecker->hasOnlyGiftCardItems($items)) {
            return false;
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
