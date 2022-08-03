<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig;
use FondOfSpryker\Yves\CheckoutPage\CheckoutPageDependencyProvider;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface;
use SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\PostConditionCheckerInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\ShipmentStep as SprykerShopShipmentStep;
use Symfony\Component\HttpFoundation\Request;

class ShipmentStep extends SprykerShopShipmentStep
{
    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig
     */
    protected $config;

    /**
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface $calculationClient
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection $shipmentPlugins
     * @param \SprykerShop\Yves\CheckoutPage\Process\Steps\PostConditionCheckerInterface $postConditionChecker
     * @param \SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface $giftCardItemsChecker
     * @param string $stepRoute
     * @param string $escapeRoute
     * @param array $checkoutShipmentStepEnterPreCheckPlugins
     * @param \FondOfSpryker\Yves\CheckoutPage\CheckoutPageConfig $config
     */
    public function __construct(
        CheckoutPageToCalculationClientInterface $calculationClient,
        StepHandlerPluginCollection $shipmentPlugins,
        PostConditionCheckerInterface $postConditionChecker,
        GiftCardItemsCheckerInterface $giftCardItemsChecker,
        $stepRoute,
        $escapeRoute,
        array $checkoutShipmentStepEnterPreCheckPlugins,
        CheckoutPageConfig $config
    ) {
        parent::__construct(
            $calculationClient,
            $shipmentPlugins,
            $postConditionChecker,
            $giftCardItemsChecker,
            $stepRoute,
            $escapeRoute,
            $checkoutShipmentStepEnterPreCheckPlugins
        );

        $this->config = $config;
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $quoteTransfer
     *
     * @return bool
     */
    public function requireInput(AbstractTransfer $quoteTransfer): bool
    {
        return false;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function execute(Request $request, AbstractTransfer $quoteTransfer)
    {
        if (!$this->executeCheckoutShipmentStepEnterPreCheckPlugins($quoteTransfer)) {
            return $quoteTransfer;
        }

        $quoteTransfer = $this->setDefaultShipmentMethod($quoteTransfer);

        $shipmentHandler = $this->shipmentPlugins->get(CheckoutPageDependencyProvider::PLUGIN_SHIPMENT_STEP_HANDLER);

        return $shipmentHandler->addToDataClass($request, $quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function postCondition(AbstractTransfer $quoteTransfer)
    {
        if (!$this->giftCardItemsChecker->hasOnlyGiftCardItems($quoteTransfer->getItems())) {
            return parent::postCondition($quoteTransfer);
        }

        foreach ($quoteTransfer->getItems() as $item) {
            $shipment = $item->getShipment();

            if ($shipment !== null && $shipment->getShipmentSelection() !== CheckoutPageConfig::SHIPMENT_METHOD_NAME_NO_SHIPMENT) {
                return false;
            }
        }

        return parent::postCondition($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function setDefaultShipmentMethod(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $defaultShipmentMethodId = (string)$this->config->getDefaultShipmentMethodId();
        $itemTransfers = $quoteTransfer->getItems();

        if ($this->giftCardItemsChecker->hasOnlyGiftCardItems($itemTransfers)) {
            $defaultShipmentMethodId = CheckoutPageConfig::SHIPMENT_METHOD_NAME_NO_SHIPMENT;
        }

        foreach ($itemTransfers as $itemTransfer) {
            if ($itemTransfer->getShipment() === null) {
                $itemTransfer->setShipment(new ShipmentTransfer());
            }

            $itemTransfer->getShipment()->setShipmentSelection($defaultShipmentMethodId);
        }

        if ($quoteTransfer->getShipment() === null) {
            $quoteTransfer->setShipment(new ShipmentTransfer());
        }

        $quoteTransfer->getShipment()->setShipmentSelection($defaultShipmentMethodId);

        return $quoteTransfer;
    }
}
