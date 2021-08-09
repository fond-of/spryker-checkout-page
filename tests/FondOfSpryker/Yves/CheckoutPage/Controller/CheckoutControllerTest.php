<?php

namespace FondOfSpryker\Yves\CheckoutPage\Controller;

use Codeception\Test\Unit;
use FondOfSpryker\Yves\CheckoutPage\CheckoutPageFactory;
use FondOfSpryker\Yves\CheckoutPage\Form\FormFactory;
use FondOfSpryker\Yves\CheckoutPage\Validator\EmptyPaymentMethodValidator;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\QuoteValidationResponseTransfer;
use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\Kernel\View\View;
use Spryker\Yves\StepEngine\Form\FormCollectionHandler;
use Spryker\Yves\StepEngine\Process\StepEngine;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCheckoutClientBridge;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToQuoteClientBridge;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CheckoutControllerTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\HttpFoundation\Request
     */
    protected $requestMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Yves\CheckoutPage\CheckoutPageFactory
     */
    protected $factoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Yves\StepEngine\Process\StepEngine
     */
    protected $stepEngineMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected $redirectResponseMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Yves\StepEngine\Form\FormCollectionHandler
     */
    protected $formCollectionHandlereMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Yves\CheckoutPage\Form\FormFactory
     */
    protected $formFactoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Yves\Kernel\View\View
     */
    protected $viewMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToQuoteClientInterface
     */
    protected $quoteClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Yves\CheckoutPage\Validator\RequestValidatorInterface
     */
    protected $emptyPaymentMethodValidatorMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCheckoutClientBridge
     */
    protected $checkoutClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteValidationResponseTransfer
     */
    protected $quoteValidationResponseTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\ItemTransfer
     */
    protected $itemTransferMock;

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Controller\CheckoutController
     */
    protected $controller;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factoryMock = $this->getMockBuilder(CheckoutPageFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->stepEngineMock = $this->getMockBuilder(StepEngine::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->redirectResponseMock = $this->getMockBuilder(RedirectResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->formCollectionHandlereMock = $this->getMockBuilder(FormCollectionHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->formFactoryMock = $this->getMockBuilder(FormFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteClientMock = $this->getMockBuilder(CheckoutPageToQuoteClientBridge::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->viewMock = $this->getMockBuilder(View::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->emptyPaymentMethodValidatorMock = $this->getMockBuilder(EmptyPaymentMethodValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutClientMock = $this->getMockBuilder(CheckoutPageToCheckoutClientBridge::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteValidationResponseTransferMock = $this->getMockBuilder(QuoteValidationResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemTransferMock = $this->getMockBuilder(ItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = new class ($this->factoryMock, $this->viewMock) extends CheckoutController {

            /**
             * @var \Spryker\Yves\Kernel\AbstractFactory
             */
            protected $factory;

            /**
             * @var \Spryker\Yves\Kernel\View\View
             */
            protected $sprykerView;

            /**
             * @param \Spryker\Yves\Kernel\AbstractFactory $factory
             */
            public function __construct(AbstractFactory $factory, View $sprykerView)
            {
                $this->factory = $factory;
                $this->sprykerView = $sprykerView;
            }

            /**
             * @return \Spryker\Yves\Kernel\AbstractFactory
             */
            protected function getFactory(): AbstractFactory
            {
                return $this->factory;
            }

            /**
             * @param array $data
             * @param string[] $widgetPlugins
             * @param string|null $template
             *
             * @return \Spryker\Yves\Kernel\View\View
             */
            protected function view(array $data = [], array $widgetPlugins = [], $template = null): View
            {
                return $this->sprykerView;
            }
        };
    }

    /**
     * @return void
     */
    public function testIndexAction(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutProcess')
            ->willReturn($this->stepEngineMock);

        $this->stepEngineMock->expects(static::atLeastOnce())
            ->method('process')
            ->with($this->requestMock)
            ->willReturn($this->redirectResponseMock);

        static::assertEquals(
            $this->redirectResponseMock,
            $this->controller->indexAction($this->requestMock)
        );
    }

    /**
     * @return void
     */
    public function testCustomerAction(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutProcess')
            ->willReturn($this->stepEngineMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutFormFactory')
            ->willReturn($this->formFactoryMock);

        $this->formFactoryMock->expects(static::atLeastOnce())
            ->method('createCustomerFormCollection')
            ->willReturn($this->formCollectionHandlereMock);

        $this->stepEngineMock->expects(static::atLeastOnce())
            ->method('process')
            ->with($this->requestMock, $this->formCollectionHandlereMock)
            ->willReturn($this->redirectResponseMock);

        static::assertEquals(
            $this->redirectResponseMock,
            $this->controller->customerAction($this->requestMock)
        );
    }

    /**
     * @return void
     */
    public function testAddressActionReturnRedirect(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutProcess')
            ->willReturn($this->stepEngineMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutFormFactory')
            ->willReturn($this->formFactoryMock);

        $this->formFactoryMock->expects(static::atLeastOnce())
            ->method('createAddressFormCollection')
            ->willReturn($this->formCollectionHandlereMock);

        $this->stepEngineMock->expects(static::atLeastOnce())
            ->method('process')
            ->with($this->requestMock, $this->formCollectionHandlereMock)
            ->willReturn($this->redirectResponseMock);

        static::assertEquals(
            $this->redirectResponseMock,
            $this->controller->addressAction($this->requestMock)
        );
    }

    /**
     * @return void
     */
    public function testAddressActionReturnView(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutProcess')
            ->willReturn($this->stepEngineMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutFormFactory')
            ->willReturn($this->formFactoryMock);

        $this->formFactoryMock->expects(static::atLeastOnce())
            ->method('createAddressFormCollection')
            ->willReturn($this->formCollectionHandlereMock);

        $this->stepEngineMock->expects(static::atLeastOnce())
            ->method('process')
            ->with($this->requestMock, $this->formCollectionHandlereMock)
            ->willReturn([]);

        static::assertEquals(
            $this->viewMock,
            $this->controller->addressAction($this->requestMock)
        );
    }

    /**
     * @return void
     */
    public function testBillingAddressActionRedirect(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('getQuoteClient')
            ->willReturn($this->quoteClientMock);

        $this->quoteClientMock->expects(static::atLeastOnce())
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutProcess')
            ->willReturn($this->stepEngineMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutFormFactory')
            ->willReturn($this->formFactoryMock);

        $this->formFactoryMock->expects(static::atLeastOnce())
            ->method('createBillingAddressFormCollection')
            ->willReturn($this->formCollectionHandlereMock);

        $this->stepEngineMock->expects(static::atLeastOnce())
            ->method('process')
            ->with($this->requestMock, $this->formCollectionHandlereMock)
            ->willReturn($this->redirectResponseMock);

        static::assertEquals(
            $this->redirectResponseMock,
            $this->controller->billingAddressAction($this->requestMock)
        );
    }

    /**
     * @return void
     */
    public function testShippingAddressActionResponse(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutProcess')
            ->willReturn($this->stepEngineMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutFormFactory')
            ->willReturn($this->formFactoryMock);

        $this->formFactoryMock->expects(static::atLeastOnce())
            ->method('createShippingAddressFormCollection')
            ->willReturn($this->formCollectionHandlereMock);

        $this->stepEngineMock->expects(static::atLeastOnce())
            ->method('process')
            ->with($this->requestMock, $this->formCollectionHandlereMock)
            ->willReturn($this->redirectResponseMock);

        static::assertEquals(
            $this->redirectResponseMock,
            $this->controller->shippingAddressAction($this->requestMock)
        );
    }

    /**
     * @return void
     */
    public function testShippingAddressActionView(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutProcess')
            ->willReturn($this->stepEngineMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutFormFactory')
            ->willReturn($this->formFactoryMock);

        $this->formFactoryMock->expects(static::atLeastOnce())
            ->method('createShippingAddressFormCollection')
            ->willReturn($this->formCollectionHandlereMock);

        $this->stepEngineMock->expects(static::atLeastOnce())
            ->method('process')
            ->with($this->requestMock, $this->formCollectionHandlereMock)
            ->willReturn([]);

        static::assertEquals(
            $this->viewMock,
            $this->controller->shippingAddressAction($this->requestMock)
        );
    }

    /**
     * @return void
     */
    public function testPaymentActionResponse(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutProcess')
            ->willReturn($this->stepEngineMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutFormFactory')
            ->willReturn($this->formFactoryMock);

        $this->formFactoryMock->expects(static::atLeastOnce())
            ->method('getPaymentFormCollection')
            ->willReturn($this->formCollectionHandlereMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createEmptyPaymentMethodValidator')
            ->willReturn($this->emptyPaymentMethodValidatorMock);

        $this->emptyPaymentMethodValidatorMock->expects(static::atLeastOnce())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn($this->requestMock);

        $this->stepEngineMock->expects(static::atLeastOnce())
            ->method('process')
            ->with($this->requestMock, $this->formCollectionHandlereMock)
            ->willReturn($this->redirectResponseMock);

        static::assertEquals(
            $this->redirectResponseMock,
            $this->controller->paymentAction($this->requestMock)
        );
    }

    /**
     * @return void
     */
    public function testPaymentActionView(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutProcess')
            ->willReturn($this->stepEngineMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutFormFactory')
            ->willReturn($this->formFactoryMock);

        $this->formFactoryMock->expects(static::atLeastOnce())
            ->method('getPaymentFormCollection')
            ->willReturn($this->formCollectionHandlereMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createEmptyPaymentMethodValidator')
            ->willReturn($this->emptyPaymentMethodValidatorMock);

        $this->emptyPaymentMethodValidatorMock->expects(static::atLeastOnce())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn($this->requestMock);

        $this->stepEngineMock->expects(static::atLeastOnce())
            ->method('process')
            ->with($this->requestMock, $this->formCollectionHandlereMock)
            ->willReturn([]);

        static::assertEquals(
            $this->viewMock,
            $this->controller->paymentAction($this->requestMock)
        );
    }

    /**
     * @return void
     */
    public function testSummaryActionRedirect(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('getQuoteClient')
            ->willReturn($this->quoteClientMock);

        $this->quoteClientMock->expects(static::atLeastOnce())
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('getCheckoutClient')
            ->willReturn($this->checkoutClientMock);

        $this->checkoutClientMock->expects(static::atLeastOnce())
            ->method('isQuoteApplicableForCheckout')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteValidationResponseTransferMock);

        $this->quoteValidationResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutProcess')
            ->willReturn($this->stepEngineMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutFormFactory')
            ->willReturn($this->formFactoryMock);

        $this->formFactoryMock->expects(static::atLeastOnce())
            ->method('createSummaryFormCollection')
            ->willReturn($this->formCollectionHandlereMock);

        $this->stepEngineMock->expects(static::atLeastOnce())
            ->method('process')
            ->with($this->requestMock, $this->formCollectionHandlereMock)
            ->willReturn($this->redirectResponseMock);

        static::assertEquals(
            $this->redirectResponseMock,
            $this->controller->summaryAction($this->requestMock)
        );
    }

    /**
     * @return void
     */
    public function testSummaryActionView(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('getQuoteClient')
            ->willReturn($this->quoteClientMock);

        $this->quoteClientMock->expects(static::atLeastOnce())
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('getCheckoutClient')
            ->willReturn($this->checkoutClientMock);

        $this->checkoutClientMock->expects(static::atLeastOnce())
            ->method('isQuoteApplicableForCheckout')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteValidationResponseTransferMock);

        $this->quoteValidationResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutProcess')
            ->willReturn($this->stepEngineMock);

        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createCheckoutFormFactory')
            ->willReturn($this->formFactoryMock);

        $this->formFactoryMock->expects(static::atLeastOnce())
            ->method('createSummaryFormCollection')
            ->willReturn($this->formCollectionHandlereMock);

        $this->stepEngineMock->expects(static::atLeastOnce())
            ->method('process')
            ->with($this->requestMock, $this->formCollectionHandlereMock)
            ->willReturn(['cartItems' => [$this->itemTransferMock]]);

        static::assertEquals(
            $this->viewMock,
            $this->controller->summaryAction($this->requestMock)
        );
    }

    /**
     * @return void
     */
    public function errorActionTest(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('getQuoteClient')
            ->willReturn($this->quoteClientMock);

        $this->quoteClientMock->expects(static::atLeastOnce())
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        static::assertEquals(
            $this->viewMock,
            $this->controller->errorAction($this->requestMock)
        );
    }
}
