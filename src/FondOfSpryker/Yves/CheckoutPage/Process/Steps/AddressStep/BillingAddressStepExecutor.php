<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps\AddressStep;

use FondOfSpryker\Yves\CheckoutPage\Resetter\OrderReferenceResetterInterface;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Service\CheckoutPageToCustomerServiceInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\AddressStep\AddressStepExecutor as SprykerAddressStepExecutor;
use Symfony\Component\HttpFoundation\Request;

class BillingAddressStepExecutor extends SprykerAddressStepExecutor
{
    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Resetter\OrderReferenceResetterInterface
     */
    protected $orderReferenceResetter;

    /**
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Service\CheckoutPageToCustomerServiceInterface $customerService
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface $customerClient
     * @param array $addressTransferExpanderPlugins
     * @param \FondOfSpryker\Yves\CheckoutPage\Resetter\OrderReferenceResetterInterface $orderReferenceResetter
     */
    public function __construct(
        CheckoutPageToCustomerServiceInterface $customerService,
        CheckoutPageToCustomerClientInterface $customerClient,
        array $addressTransferExpanderPlugins,
        OrderReferenceResetterInterface $orderReferenceResetter,
    ) {
        parent::__construct($customerService, $customerClient, $addressTransferExpanderPlugins);

        $this->orderReferenceResetter = $orderReferenceResetter;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function execute(Request $request, QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $customerTransfer = $this->getCustomerTransfer() ?? $quoteTransfer->getCustomer();
        $customerTransfer = $this->updateCustomerDataFromBillingAddress($customerTransfer, $quoteTransfer);

        if ($quoteTransfer->getCustomer() === null) {
            $quoteTransfer->setCustomer($customerTransfer);
        }

        $quoteTransfer = $this->hydrateBillingAddressWithQuoteLevelData($quoteTransfer, $customerTransfer);

        $quoteTransfer = $this->orderReferenceResetter->reset($quoteTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function updateCustomerDataFromBillingAddress(CustomerTransfer $customerTransfer, QuoteTransfer $quoteTransfer): CustomerTransfer
    {
        $billingAddress = $quoteTransfer->getBillingAddress();
        $customerTransfer->fromArray($billingAddress->toArray(), true);

        return $customerTransfer;
    }
}
