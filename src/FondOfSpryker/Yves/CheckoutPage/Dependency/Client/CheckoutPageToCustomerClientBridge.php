<?php

namespace FondOfSpryker\Yves\CheckoutPage\Dependency\Client;

use Generated\Shared\Transfer\CustomerTransfer;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientBridge as SprykerShopCheckoutPageToCustomerClientBridge;
use FondOfSpryker\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface;

class CheckoutPageToCustomerClientBridge extends SprykerShopCheckoutPageToCustomerClientBridge implements CheckoutPageToCustomerClientInterface
{

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function getCustomerByEmail(CustomerTransfer $customerTransfer): CustomerTransfer
    {
        return $this->customerClient->getCustomerByEmail($customerTransfer);
    }
}
