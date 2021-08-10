<?php

namespace FondOfSpryker\Yves\CheckoutPage\Process\Steps;

use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PayoneGetPaymentDetailTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\Sales\SalesClient;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Client\Payone\PayoneClientInterface;
use SprykerEco\Yves\Payone\Handler\PayoneHandler;
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
     * @var \Spryker\Client\Sales\SalesClient
     */
    protected $salesClient;

    /**
     * @var \SprykerEco\Client\Payone\PayoneClientInterface
     */
    protected $payoneClient;

    /**
     * @param \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface $customerClient
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCartClientInterface $cartClient
     * @param \SprykerShop\Yves\CheckoutPage\CheckoutPageConfig $checkoutPageConfig
     * @param \SprykerEco\Client\Payone\PayoneClientInterface $payoneClient
     * @param \Spryker\Client\Sales\SalesClient $salesClient
     * @param string $stepRoute
     * @param string $escapeRoute
     */
    public function __construct(
        CheckoutPageToCustomerClientInterface $customerClient,
        CheckoutPageToCartClientInterface $cartClient,
        CheckoutPageConfig $checkoutPageConfig,
        PayoneClientInterface $payoneClient,
        SalesClient $salesClient,
        string $stepRoute,
        string $escapeRoute
    ) {
        parent::__construct($customerClient, $cartClient, $checkoutPageConfig, $stepRoute, $escapeRoute);

        $this->customerClient = $customerClient;
        $this->stepRoute = $stepRoute;
        $this->cartClient = $cartClient;
        $this->checkoutPageConfig = $checkoutPageConfig;

        $this->payoneClient = $payoneClient;
        $this->salesClient = $salesClient;
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

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $dataTransfer
     *
     * @return array
     */
    public function getTemplateVariables(AbstractTransfer $dataTransfer): array
    {
        $getPaymentDetailTransfer = new PayoneGetPaymentDetailTransfer();

        if ($this->quoteTransfer->getPayment()->getPaymentProvider() === PayoneHandler::PAYMENT_PROVIDER) {
            $getPaymentDetailTransfer->setOrderReference($this->quoteTransfer->getOrderReference());
            $getPaymentDetailTransfer = $this->payoneClient->getPaymentDetail($getPaymentDetailTransfer);
        }

        $customerTransfer = $this->customerClient->getCustomerByEmail($this->quoteTransfer->getCustomer());

        $orderTransfer = new OrderTransfer();
        $orderTransfer->setIdSalesOrder($this->quoteTransfer->getIdSalesOrder());
        $orderTransfer->setFkCustomer($customerTransfer->getIdCustomer());
        $orderTransfer = $this->salesClient->getOrderDetails($orderTransfer);

        return [
            'orderTransfer' => $orderTransfer,
            'quoteTransfer' => $this->quoteTransfer,
            'paymentDetail' => $getPaymentDetailTransfer->getPaymentDetail(),
        ];
    }
}
