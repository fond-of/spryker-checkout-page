<?php

namespace FondOfSpryker\Yves\CheckoutPage\Form\DataProvider;

use FondOfSpryker\Yves\CheckoutPage\Dependency\CheckoutStoreCountryDataProviderInterface;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryInterface;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientBridge;
use FondOfSpryker\Yves\CheckoutPage\Form\CheckoutBillingAddressForm;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface;

class CheckoutBillingAddressFormDataProvider implements StepEngineFormDataProviderInterface
{
    /**
     * @var string
     */
    public const OPTION_ADDRESS_CHOICES = 'address_choices';

    /**
     * @var string
     */
    public const OPTION_COUNTRY_CHOICES = 'country_choices';

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientBridge
     */
    protected $customerClient;

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryInterface
     */
    protected $countryClient;

    /**
     * @var \FondOfSpryker\Yves\CheckoutPage\Dependency\CheckoutStoreCountryDataProviderInterface
     */
    protected $countryDataProvider;

    /**
     * @var \SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface
     */
    protected $giftCardItemsChecker;

    /**
     * @param \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientBridge $customerClient
     * @param \FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCountryInterface $countryClient
     * @param \FondOfSpryker\Yves\CheckoutPage\Dependency\CheckoutStoreCountryDataProviderInterface $countryDataProvider
     * @param \SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface $giftCardItemsChecker
     */
    public function __construct(
        CheckoutPageToCustomerClientBridge $customerClient,
        CheckoutPageToCountryInterface $countryClient,
        CheckoutStoreCountryDataProviderInterface $countryDataProvider,
        GiftCardItemsCheckerInterface $giftCardItemsChecker
    ) {
        $this->customerClient = $customerClient;
        $this->countryClient = $countryClient;
        $this->countryDataProvider = $countryDataProvider;
        $this->giftCardItemsChecker = $giftCardItemsChecker;
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer|\Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function getData(AbstractTransfer $quoteTransfer)
    {
        if ($quoteTransfer instanceof QuoteTransfer) {
            $quoteTransfer->setBillingAddress($this->getBillingAddress($quoteTransfer));
        }

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    public function getOptions(AbstractTransfer $quoteTransfer): array
    {
        return [
            CheckoutBillingAddressForm::OPTION_ADDRESS_CHOICES => $this->getAddressChoices(),
            CheckoutBillingAddressForm::OPTION_COUNTRY_CHOICES => $this->getAvailableCountries($quoteTransfer),
            CheckoutBillingAddressForm::COUNTRY_CLIENT => $this->countryClient,
            CheckoutBillingAddressForm::OPTION_SALUTATIONS => $this->getSalutationOptions(),
            CheckoutBillingAddressForm::OPTION_GIFT_CARD_ONLY_CARD => $this->giftCardItemsChecker->hasOnlyGiftCardItems(
                $quoteTransfer->getItems(),
            ),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function getBillingAddress(QuoteTransfer $quoteTransfer)
    {
        $billingAddressTransfer = new AddressTransfer();
        if ($quoteTransfer->getBillingAddress() !== null) {
            $billingAddressTransfer = $quoteTransfer->getBillingAddress();
        }

        $this->customerClient->markCustomerAsDirty();

        $customerTransfer = $this->customerClient->getCustomer();

        if ($customerTransfer !== null && $quoteTransfer->getBillingAddress() === null) {
            $billingAddressTransfer->setIdCustomerAddress((int)$customerTransfer->getDefaultBillingAddress());
        }

        return $billingAddressTransfer;
    }

    /**
     * @return array
     */
    protected function getAddressChoices()
    {
        $this->customerClient->markCustomerAsDirty();

        $customerTransfer = $this->customerClient->getCustomer();

        if ($customerTransfer === null) {
            return [];
        }

        $customerAddressesTransfer = $customerTransfer->getAddresses();

        if ($customerAddressesTransfer === null) {
            return [];
        }

        $choices = [];
        foreach ($customerAddressesTransfer->getAddresses() as $address) {
            $choices[$address->getIdCustomerAddress()] = sprintf(
                '%s %s %s, %s %s, %s %s',
                $address->getSalutation(),
                $address->getFirstName(),
                $address->getLastName(),
                $address->getAddress1(),
                $address->getAddress2(),
                $address->getZipCode(),
                $address->getCity(),
            );
        }

        return $choices;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    protected function getAvailableCountries(QuoteTransfer $quoteTransfer): array
    {
        return $this->countryDataProvider->getCountries($quoteTransfer);
    }

    /**
     * @return array
     */
    protected function getSalutationOptions(): array
    {
        return [
            'Mr' => 'customer.salutation.mr',
            'Ms' => 'customer.salutation.ms',
            'Diverse' => 'customer.salutation.diverse',
        ];
    }
}
