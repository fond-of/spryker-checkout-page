<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Step\StepWithBreadcrumbInterface;
use Spryker\Yves\StepEngine\Dependency\Step\StepWithExternalRedirectInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\CustomerStep as SprykerShopCustomerStep;
use Symfony\Component\HttpFoundation\Request;

class CustomerStep extends SprykerShopCustomerStep implements StepWithBreadcrumbInterface, StepWithExternalRedirectInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer|void
     */
    public function execute(Request $request, AbstractTransfer $quoteTransfer)
    {
        if (empty($quoteTransfer->getCustomer())) {
            $customerTransfer = new CustomerTransfer();
            $customerTransfer->setIsGuest(true);
            $quoteTransfer->setCustomer($customerTransfer);
        }

        return $this->customerStepHandler->addToDataClass($request, $quoteTransfer);
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
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $quoteTransfer
     *
     * @return bool
     */
    public function postCondition(AbstractTransfer $quoteTransfer): bool
    {
        if ($this->isCustomerInQuote($quoteTransfer) === false) {
            return false;
        }

        if ($this->isGuestCustomerSelected($quoteTransfer) && $this->isCustomerLoggedIn()) {
            // override guest user with logged in user
            return false;
        }

        $customerTransfer = $this->customerClient->getCustomer();

        if ($customerTransfer) {
            $customerTransfer = $this->customerClient->findCustomerById($customerTransfer);

            if (!$customerTransfer) {
                $this->externalRedirect = $this->logoutRoute;

                return false;
            }
        }

        return true;
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $quoteTransfer
     *
     * @return bool
     */
    public function isBreadcrumbItemHidden(AbstractTransfer $quoteTransfer)
    {
        return true;
    }
}
