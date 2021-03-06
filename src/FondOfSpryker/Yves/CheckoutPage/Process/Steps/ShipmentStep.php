<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use FondOfSpryker\Yves\CheckoutPage\CheckoutPageDependencyProvider;
use FondOfSpryker\Yves\Shipment\ShipmentConfig;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Shipment\ShipmentConstants;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\ShipmentStep as SprykerShopShipmentStep;
use Symfony\Component\HttpFoundation\Request;

class ShipmentStep extends SprykerShopShipmentStep
{
    /**
     * @var \FondOfSpryker\Yves\Shipment\ShipmentConfig
     */
    protected $shipmentConfig;

    public function __construct(
        CheckoutPageToCalculationClientInterface $calculationClient,
        StepHandlerPluginCollection $shipmentPlugins,
        ShipmentConfig $shipmentConfig,
        string $stepRoute,
        string $escapeRoute
    ) {
        parent::__construct($calculationClient, $shipmentPlugins, $stepRoute, $escapeRoute);

        $this->shipmentConfig = $shipmentConfig;
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
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function execute(Request $request, AbstractTransfer $quoteTransfer): QuoteTransfer
    {
        if (!$this->requireInput($quoteTransfer)) {
            $quoteTransfer = $this->setDefaultShipmentMethod($quoteTransfer);
        }

        $shipmentHandler = $this->shipmentPlugins->get(CheckoutPageDependencyProvider::PLUGIN_SHIPMENT_STEP_HANDLER);
        $shipmentHandler->addToDataClass($request, $quoteTransfer);

        return $this->calculationClient->recalculate($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function setDefaultShipmentMethod(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $defaultShipmentMehtodId = $this->shipmentConfig->getDefaultShipmentMethodId();

        $shipmentTransfer = (new ShipmentTransfer())
            ->setShipmentSelection($defaultShipmentMehtodId);

        $quoteTransfer->setShipment($shipmentTransfer);

        return $quoteTransfer;
    }

    protected function isShipmentSet(QuoteTransfer $quoteTransfer): bool
    {
        foreach ($quoteTransfer->getExpenses() as $expenseTransfer) {
            if ($expenseTransfer->getType() === ShipmentConstants::SHIPMENT_EXPENSE_TYPE) {
                return $quoteTransfer->getShipment()->getShipmentSelection() !== null;
            }
        }

        return false;
    }
}
