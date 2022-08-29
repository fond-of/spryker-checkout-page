<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerShop\Yves\CheckoutPage\CheckoutPageConfig;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCartClientInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\SuccessStep as SprykerShopSuccessStep;
use Symfony\Component\HttpFoundation\Request;

class SuccessStep extends SprykerShopSuccessStep
{
    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface
     */
    protected $customerClient;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransfer;

    /**
     * @param \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface $customerClient
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCartClientInterface $cartClient
     * @param \SprykerShop\Yves\CheckoutPage\CheckoutPageConfig $checkoutPageConfig
     * @param string $stepRoute
     * @param string $escapeRoute
     */
    public function __construct(
        CheckoutPageToCustomerClientInterface $customerClient,
        CheckoutPageToCartClientInterface $cartClient,
        CheckoutPageConfig $checkoutPageConfig,
        string $stepRoute,
        string $escapeRoute
    ) {
        parent::__construct($customerClient, $cartClient, $checkoutPageConfig, $stepRoute, $escapeRoute);

        $this->customerClient = $customerClient;
        $this->stepRoute = $stepRoute;
        $this->cartClient = $cartClient;
        $this->checkoutPageConfig = $checkoutPageConfig;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function execute(Request $request, AbstractTransfer $quoteTransfer)
    {
        $this->customerClient->markCustomerAsDirty();

        if ($this->checkoutPageConfig->cleanCartAfterOrderCreation()) {
            $this->cartClient->clearQuote();
        }

        if ($quoteTransfer instanceof QuoteTransfer) {
            $this->quoteTransfer = $quoteTransfer;
        }

        return new QuoteTransfer();
    }
}
