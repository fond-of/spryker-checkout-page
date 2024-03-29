<?php

namespace FondOfSpryker\Yves\CheckoutPage\Controller;

use FondOfSpryker\Shared\Customer\CustomerConstants;
use SprykerShop\Yves\CheckoutPage\Controller\CheckoutController as SprykerShopCheckoutController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \FondOfSpryker\Yves\CheckoutPage\CheckoutPageFactory getFactory()
 */
class CheckoutController extends SprykerShopCheckoutController
{
    /**
     * @var string
     */
    protected const PATTERN_CHECKOUT_BILLING_ADDRESS = '/^(\/[a-z]{2})?\/checkout\/billing-address(\/)?/';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function indexAction(Request $request)
    {
        return $this->createStepProcess()->process($request);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    public function customerAction(Request $request)
    {
        $response = $this->createStepProcess()->process(
            $request,
            $this->getFactory()
                ->createCheckoutFormFactory()
                ->createCustomerFormCollection(),
        );

        return $response;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    public function addressAction(Request $request)
    {
        $response = $this->createStepProcess()->process(
            $request,
            $this->getFactory()
                ->createCheckoutFormFactory()
                ->createAddressFormCollection(),
        );

        if (!is_array($response)) {
            return $response;
        }

        return $this->view(
            $response,
            $this->getFactory()->getCustomerPageWidgetPlugins(),
            '@CheckoutPage/views/address/address.twig',
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    public function billingAddressAction(Request $request)
    {
        $quoteClient = $this->getFactory()->getQuoteClient();
        $quoteTransfer = $quoteClient->getQuote();

        $response = $this->createStepProcess()->process(
            $request,
            $this->getFactory()
                ->createCheckoutFormFactory()
                ->createBillingAddressFormCollection(),
        );

        if (!is_array($response)) {
            return $response;
        }

        $response['countriesInEU'] = CustomerConstants::COUNTRIES_IN_EU;
        $response['billingSameAsShipping'] = true;

        if ($quoteTransfer->getBillingAddress() !== null && $quoteTransfer->getBillingAddress()->getEmail() !== null) {
            $response['billingSameAsShipping'] = $quoteTransfer->getBillingSameAsShipping();
        }

        return $this->view(
            $response,
            $this->getFactory()->getCustomerPageWidgetPlugins(),
            '@CheckoutPage/views/billing-address/billing-address.twig',
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Spryker\Yves\Kernel\View\View|\Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function shippingAddressAction(Request $request)
    {
        $referer = $request->server->get('HTTP_REFERER');

        if ($referer !== null && preg_match(static::PATTERN_CHECKOUT_BILLING_ADDRESS, Request::create($referer)->getPathInfo()) === false) {
            $quoteClient = $this->getFactory()->getQuoteClient();
            $quoteTransfer = $quoteClient->getQuote();
            $quoteTransfer->setBillingSameAsShipping($quoteTransfer->getBillingSameAsShipping() ?? false);
            $quoteClient->setQuote($quoteTransfer);
        }

        $response = $this->createStepProcess()->process(
            $request,
            $this->getFactory()
                ->createCheckoutFormFactory()
                ->createShippingAddressFormCollection(),
        );

        if (!is_array($response)) {
            return $response;
        }

        return $this->view(
            $response,
            [],
            '@CheckoutPage/views/shipping-address/shipping-address.twig',
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    public function paymentAction(Request $request)
    {
        $response = $this->createStepProcess()->process(
            $this->getFactory()->createEmptyPaymentMethodValidator()->validate($request),
            $this->getFactory()
                ->createCheckoutFormFactory()
                ->getPaymentFormCollection(),
        );

        if (!is_array($response)) {
            return $response;
        }

        return $this->view(
            $response,
            $this->getFactory()->getCustomerPageWidgetPlugins(),
            '@CheckoutPage/views/payment/payment.twig',
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    public function summaryAction(Request $request)
    {
        $quoteValidationResponseTransfer = $this->canProceedCheckout();

        if (!$quoteValidationResponseTransfer->getIsSuccessful()) {
            $this->processErrorMessages($quoteValidationResponseTransfer->getMessages());

            return $this->redirectResponseInternal(static::ROUTE_CART);
        }

        $taxInPercent = [];
        $viewData = $this->createStepProcess()->process(
            $request,
            $this->getFactory()
                ->createCheckoutFormFactory()
                ->createSummaryFormCollection(),
        );

        if (!is_array($viewData)) {
            return $viewData;
        }

        /** @var \Generated\Shared\Transfer\ItemTransfer $item */
        foreach ($viewData['cartItems'] as $item) {
            if (in_array($item->getTaxRate(), $taxInPercent)) {
                continue;
            }

            $taxInPercent[] = $item->getTaxRate();
        }

        return $this->view(
            array_merge($viewData, ['taxInPercent' => $taxInPercent]),
            $this->getFactory()->getSummaryPageWidgetPlugins(),
            '@CheckoutPage/views/summary/summary.twig',
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Spryker\Yves\Kernel\View\View
     */
    public function errorAction(?Request $request = null)
    {
        $quoteClient = $this->getFactory()->getQuoteClient();
        $quoteTransfer = $quoteClient->getQuote();

        return $this->view(
            ['quoteTransfer' => $quoteTransfer],
            [],
            '@CheckoutPage/views/order-fail/order-fail.twig',
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Process\StepEngineInterface
     */
    protected function createStepProcess()
    {
        return $this->getFactory()->createCheckoutProcess();
    }
}
