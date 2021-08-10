<?php

namespace FondOfSpryker\Yves\CheckoutPage\Dependency\Client;

use Codeception\Test\Unit;
use FondOfSpryker\Client\Country\CountryClient;
use Generated\Shared\Transfer\CountryTransfer;

class CheckoutPageToCountryBridgeTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Client\Country\CountryClientInterface
     */
    protected $countryClientMock;

    /**
     * @var \Generated\Shared\Transfer\CountryTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $countryTransferMock;

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryInterface
     */
    protected $bridge;

    /**
     * @required void
     *
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->countryClientMock = $this->getMockBuilder(CountryClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->countryTransferMock = $this->getMockBuilder(CountryTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->bridge = new CheckoutPageToCountryBridge($this->countryClientMock);
    }

    /**
     * @return void
     */
    public function testGetRegionsByCountryTransfer(): void
    {
        $this->countryClientMock->expects(static::atLeastOnce())
            ->method('getRegionsByCountryTransfer')
            ->with($this->countryTransferMock)
            ->willReturn($this->countryTransferMock);

        static::assertEquals(
            $this->countryTransferMock,
            $this->bridge->getRegionsByCountryTransfer($this->countryTransferMock)
        );
    }
}
