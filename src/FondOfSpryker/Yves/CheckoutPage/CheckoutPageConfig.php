<?php

namespace FondOfSpryker\Yves\CheckoutPage;

use SprykerShop\Yves\CheckoutPage\CheckoutPageConfig as SprykerCheckoutPageConfig;

class CheckoutPageConfig extends SprykerCheckoutPageConfig
{
    /**
     * @return bool
     */
    public function cleanCartAfterOrderCreation()
    {
        return false;
    }
}
