# fond-of-spryker/google-custom-search
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/fond-of-spryker/google-custom-search)

Remove customer registration from checkout.

## Install

```
composer require fond-of-spryker/checkout-page
```

### Configuration

Extend the existing CheckoutPageDependencyProvider with the new one from the package

```
use FondOfSpryker\Yves\CheckoutPage\CheckoutPageDependencyProvider as FondOfSprykerCheckoutPageDependencyProvider;

class CheckoutPageDependencyProvider extends FondOfSprykerCheckoutPageDependencyProvider
```

Go to the YvesBootstrap.php and replace the provider Plugin:

```
use SprykerShop\Yves\CheckoutPage\Plugin\Provider\CheckoutPageControllerProvider;
use FondOfSpryker\Yves\CheckoutPage\Plugin\Provider\CheckoutPageControllerProvider;
```