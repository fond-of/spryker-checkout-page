# fond-of-spryker/fond-of-spryker/checkout-page
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/fond-of-spryker/google-custom-search)

Removes the customer registration from checkout. Every Customer will be 
saved after placing order. On another order form the same customer the 
email address will be matched. So you get a history of all orders from 
the same customer in ZED

You need to implement 3 packages:
- fond-of-spryker/checkout-page
- fond-of-spryker/customer-page
- fond-of-spryker/customer

## Install

All packages are depended on each other

```
composer require fond-of-spryker/checkout-page
```

### Configuration

Extend the existing CheckoutPageDependencyProvider with the new one 
from fond-of-spryker/checkout-page:

```
namespace Pyz\Yves\CheckoutPage;;

use FondOfSpryker\Yves\CheckoutPage\CheckoutPageDependencyProvider as FondOfSprykerCheckoutPageDependencyProvider;

class CheckoutPageDependencyProvider extends FondOfSprykerCheckoutPageDependencyProvider
```

Go to the YvesBootstrap.php and replace the provider Plugin:

```
use SprykerShop\Yves\CheckoutPage\Plugin\Provider\CheckoutPageControllerProvider;
use FondOfSpryker\Yves\CheckoutPage\Plugin\Provider\CheckoutPageControllerProvider;
```

At next, do the same with CustomerPageDependencyProvider:

```
namespace Pyz\Yves\CustomerPage;

use FondOfSpryker\Yves\CustomerPage\CustomerPageDependencyProvider as FondOfSprykerCustomerPageDependencyProvider;

class CustomerPageDependencyProvider extends FondOfSprykerCustomerPageDependencyProvider
```

At least we need to configure ZED:

```
namespace Pyz\Zed\Customer;

use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use FondOfSpryker\Shared\Customer\CustomerConstants;
use Spryker\Zed\Customer\CustomerConfig as BaseCustomerConfig;

class CustomerConfig extends BaseCustomerConfig
{
    /**
     * @return \Generated\Shared\Transfer\SequenceNumberSettingsTransfer
     */
    public function getCustomerReferenceDefaults()
    {
        $sequenceNumberSettingsTransfer = new SequenceNumberSettingsTransfer();

        $sequenceNumberSettingsTransfer->setName(CustomerConstants::NAME_CUSTOMER_REFERENCE);

        $sequenceNumberPrefixParts = [];
        $sequenceNumberPrefixParts[] = $this->get(CustomerConstants::CUSTOMER_REFERENCE_PREFIX);

        $prefix = implode($this->getUniqueIdentifierSeparator(), $sequenceNumberPrefixParts) . $this->getUniqueIdentifierSeparator();

        $sequenceNumberSettingsTransfer->setPrefix($prefix);
        $sequenceNumberSettingsTransfer->setOffset($this->get(CustomerConstants::CUSTOMER_REFERENCE_OFFSET));

        return $sequenceNumberSettingsTransfer;
    }
}
```


