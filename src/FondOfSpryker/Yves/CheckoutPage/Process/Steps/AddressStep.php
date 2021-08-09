<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Generated\Shared\Transfer\AddressTransfer;
use SprykerShop\Yves\CheckoutPage\CheckoutPageConfig;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\AddressStep as SprykerShopAddressStep;
use SprykerShop\Yves\CheckoutPage\Process\Steps\PostConditionCheckerInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\StepExecutorInterface;

class AddressStep extends SprykerShopAddressStep
{
    /**
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface $calculationClient
     * @param string $stepRoute
     * @param string|null $escapeRoute
     * @param \SprykerShop\Yves\CheckoutPage\Process\Steps\StepExecutorInterface $stepExecutor
     * @param \SprykerShop\Yves\CheckoutPage\Process\Steps\PostConditionCheckerInterface $postConditionChecker
     * @param \SprykerShop\Yves\CheckoutPage\CheckoutPageConfig $checkoutPageConfig
     * @param array $checkoutAddressStepEnterPreCheckPlugins
     */
    public function __construct(
        CheckoutPageToCalculationClientInterface $calculationClient,
        $stepRoute,
        $escapeRoute,
        StepExecutorInterface $stepExecutor,
        PostConditionCheckerInterface $postConditionChecker,
        CheckoutPageConfig $checkoutPageConfig,
        array $checkoutAddressStepEnterPreCheckPlugins
    ) {
        parent::__construct(
            $calculationClient,
            $stepExecutor,
            $postConditionChecker,
            $checkoutPageConfig,
            $stepRoute,
            $escapeRoute,
            $checkoutAddressStepEnterPreCheckPlugins
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer|null $addressTransfer
     *
     * @return bool
     */
    protected function isAddressEmpty(?AddressTransfer $addressTransfer = null)
    {
        if ($addressTransfer === null) {
            return true;
        }

        $hasName = (!empty($addressTransfer->getFirstName()) && !empty($addressTransfer->getLastName()));
        if ($addressTransfer->getIdCustomerAddress() === null && $hasName === false) {
            return true;
        }

        return false;
    }
}
