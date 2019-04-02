# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [2.0.10] - 2019-04-02

### Fixed

- Move redirect below hydration of session variables (If an item is unavailable on the backend and not passed back, we want to update the front end before redirecting, else the item is not removed from the cart)

## [2.0.9] - 2019-04-02

### Fixed

- Added exit call after redirect
- Ensured session was persisted with Session::save()

## [2.0.8] - 2019-04-02

### Changed

- Ensure an order meets a minimim order value

## [2.0.7] - 2019-03-26

### Changed

- Added ids to the cart checkout buttons for easier GTM tracking

## [2.0.5] - 2019-01-21

### Changed

- Reduced restrictions on package requirements to improve compatibility with projects

## [2.0.4] - 2018-12-14

### Changed

- Show full address in UI after returning from PayPal

### Fixed

- Handle PayPal Auth nicely and log errors

## [2.0.3] - 2018-12-13

### Changed

- Allow delivery term to be translatable
- Optionally include upsells view

## [2.0.2] - 2018-10-30

### Changed

- Refactor delivery date selector
- Strip invisible characters in `toLocaleDateString()`

## [2.0.1] - 2018-10-02

### Changed

- Default discount invalid message prefix removed

## [2.0.0] - 2018-10-01

### Changed

- Calendar based delivery selector

## [1.8.0] - 2018-09-20

### Changed

- Custom discount messages
- Hide billing details on free order

## [1.7.0] - 2018-09-17

### Changed

- Handle both monetary and percentage discounts
- Pass discount data back to front end

## [1.7.0] - 2018-08-13

### Changed

- Avoid processing payments on orders which have already been paid

## [1.6.0] - 2018-08-15

### Changed

- Add caching to only process a single payment for an order even if multiple processes are trying (button mashing)

## [1.5.4] - 2018-07-23

### Fixed

- Correctly display name on card checkout validation error message

## [1.5.3] - 2018-07-18

### Added

- Stripe idempotency key to avoid duplicate payments

### Changed

- Stripe component now caches the token itself and invalidates it when the user updates the card details. Now, a new token is only requested if the card details have changed, not everytime the submit button is pressed.

## [1.5.2] - 2018-07-02

### Fixed

- The discount code in the Cart would always throw a promise error if it was invalid. Added catch.
- Remove object destructuring from blade templates for IE compatibility

## [1.5.1] - 2018-06-06

### Changed

- Make county an optional field

## [1.5.0] - 2018-06-01

### Added

- Post Code address lookup functionality with PCA Predict (Postcode Anywhere)

## [1.4.3] - 2018-05-22

### Changed

- Append payment response to checkout payload

## [1.4.2] - 2018-05-16

### Changed

- Add order server stage to JS server variables

## [1.4.1] - 2018-04-30

### Changed

- Made all PayPal fields used in the `propagatePaypal()` method optional, incase they aren't returned from the customers PayPal account.

## [1.4.0] - 2018-04-25

### Changed

- Removed hard-coded path to 'Terms & Conditions' page in checkout and replaced with the `route` helper function.
- Ensured text inside 'Terms & Conditions' link in checkout is translatable.
- Added class to 'Terms & Conditions' link in checkout in order to style element more easily.
- Improved readability of 'Terms & Conditions' link markup in checkout.

## [1.3.2] - 2018-04-24

### Changed

- Removed double pound sign from header on mobile

## [1.3.1] - 2018-04-23

### Changed

- Added notes field to checkout

## [1.2.3] - 2018-04-20

### Changed

- Fix IE11 cart

## [1.2.2] - 2018-03-15

### Changed

- Removed double pound symbols
- Updated checkout terms validation message
- Ensure values passed to Omnipay are rounded to 2dp
- Get checkout UID from session for index route

## [1.2.1] - 2018-03-06

### Added

- Minimum order value to shopping cart
- Included missing PayPal handler

## [1.1.0] - 2018-02-21

### Added

- Postage interface to allow for custom postage calculations
- Support for processing orders without expecting payment when the total is zero
- Included missing order complete blade view

### Changed

- Google Tag Manager render function now simply returns an array to allow projects to decide which tag manager / ga component to use. Breaking change.
- A blank checkout notice blade view can be used to display a custom message on the order complete screen which was previously hard-coded.

## [1.0.0] - 2018-02-21

### Added

- This CHANGELOG file to hopefully serve as an evolving example of a
  standardized open source project CHANGELOG.
- Added version numbering to better manage compatibility
