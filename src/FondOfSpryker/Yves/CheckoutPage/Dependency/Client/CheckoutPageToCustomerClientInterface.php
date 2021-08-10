<?php

namespace FondOfSpryker\Yves\CheckoutPage\Dependency\Client;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface as SprykerShopCheckoutPageToCustomerClientInterface;

interface CheckoutPageToCustomerClientInterface extends SprykerShopCheckoutPageToCustomerClientInterface
{
    /**
     * @return void
     */
    public function markCustomerAsDirty();

    /**
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
    public function getCustomer();

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function getAddress(AddressTransfer $addressTransfer);

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
    public function findCustomerById(CustomerTransfer $customerTransfer);

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function getCustomerByEmail(CustomerTransfer $customerTransfer): CustomerTransfer;
}
