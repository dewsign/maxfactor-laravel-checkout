# Maxfactor Laravel Checkout

[![Packagist](https://img.shields.io/packagist/v/maxfactor/checkout.svg?style=for-the-badge)](https://packagist.org/packages/maxfactor/checkout)

Authors:

* [Marco Mark](mailto:marco.mark@dewsign.co.uk)
* [Tristan Ward](mailto:tristan.ward@dewsign.co.uk)
* [Sam Wrigley](mailto:sam.wrigley@dewsign.co.uk)

## Overview

A Laravel checkout companion to work with Maxfactor Vue Cart.

### Dependencies

* [Maxfactor Vue Cart 1.0.0](https://github.com/dewsign/maxfactor-vue-cart)

### Configuration 

Environment configuration:

This package exposes the following configuration options:

`pca_key`: Postcode Anywhere Key, set if this service is being used
`minimum_order`: Order must reach this value to be processable.  Defaults to £1.00.

If the configuration is published the default Checkout and Postage models can be changed.  See development notes for more about this.

### Development notes

In the host project you will typically use the `HandlesCheckout` trait on a `Checkout` model.  This will allow you to access the checkout data via the `getCurrentCheckoutParams()` method.  This can then be processed in accordance with your projects order generation needs.

Checkout data accessed with the `getCurrentCheckoutParams()` method includes user and product and information.

The `CheckoutController` in this package uses a `show()` and `store()` method which is applied to each stage of the checkout to show the view and store the content repectively.  The show and store functionality is performed by the `HandlesCheckout` trait.  Custom functionality can be achieved by overloading these methods on the Checkout model implementing it in your project.

This package also provides a `HandlesPostage` trait which can be implemented in a similar fashion to the `HandlesCheckout` trait.  This allows you to use custom delivery date configurations in your host application.

A flow diagram is included in this repository to illustrate what is intended to happen at each stage of the checkout and the methods called to achieve this. [Flowmap](https://github.com/dewsign/maxfactor-laravel-checkout/blob/master/flowdiagram.svg)