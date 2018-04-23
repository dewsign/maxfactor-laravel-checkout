# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.4] - 2018-04-23

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
