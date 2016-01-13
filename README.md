# Chargify SDK for PHP

[![Total Downloads](https://img.shields.io/packagist/dt/chargely/chargify-sdk-php.svg?style=flat)](https://packagist.org/packages/chargely/chargify-sdk-php)
[![Build Status](https://img.shields.io/travis/chargely/chargify-sdk-php.svg?style=flat)](https://travis-ci.org/chargely/chargify-sdk-php)
[![Latest Stable Version](https://img.shields.io/packagist/v/chargely/chargify-sdk-php.svg?style=flat)](https://packagist.org/packages/chargely/chargify-sdk-php)
[![Apache 2 License](https://img.shields.io/packagist/l/chargely/chargify-sdk-php.svg?style=flat)](https://github.com/chargely/chargify-sdk-php/blob/master/LICENSE.md)
[![Documentation Status](https://readthedocs.org/projects/chargify-sdk-for-php/badge/?version=latest)](http://chargify-sdk-php.chargely.com/en/latest/?badge=latest)

This library helps you interact with the Chargify API using PHP. It has been used in production for many years by our 
flagship product, [Chargley, a billing portal for Chargify][chargely-homepage].

- Abstracts away underlying HTTP requests to the Chargify API
- Supports Chargify API v1 and Chargify Direct (v2)
- [Well documented][documentation]
- Unit tested

# Quick Example

Create a new customer.

```php
<?php
require 'vendor/autoload.php';

use Crucial\Service\Chargify;

$chargify = new Chargify([
    'hostname'   => 'yoursubdomain.chargify.com',
    'api_key'    => '{{API_KEY}}',
    'shared_key' => '{{SHARED_KEY}}'
]);

// Crucial\Service\Chargify\Customer
$customer = $chargify->customer()
    // set customer properties
    ->setFirstName('Dan')
    ->setLastName('Bowen')
    ->setEmail('dan@mailinator.com')
    // send the create request
    ->create();

// check for errors
if ($customer->isError()) {
    // array of errors loaded during the transfer
    $errors = $customer->getErrors();
 } else {
    // the transfer was successful
    $customerId = $customer['id']; // Chargify customer ID
    $firstName  = $customer['first_name'];
    $lastName   = $customer['last_name'];
    $email      = $customer['email'];
 }

```

# Help and Documentation

- [Documentation][documentation]
- [Issues][issues]

# Installation

Using [Composer][composer-homepage] is the recommended way to install the Chargify SDK for PHP. Composer is a 
dependency management tool for PHP that allows you to declare the dependencies your project needs and installs them 
into your project. In order to use the SDK with Composer, you must do the following:

Add "chargely/chargify-sdk-php" as a dependency in your project's composer.json file.

```json
{
  "require": {
      "chargely/chargify-sdk-php": "0.1.1"
  }
}
```

Download and install Composer, if you don't already have it.

```bash
curl -sS https://getcomposer.org/installer | php
```

Install your dependencies, including your newly added Chargify SDK for PHP.

```bash
php composer.phar install
```

Require Composer's autoloader

```php
<?php
require '/path/to/vendor/autoload.php';
```
    
# Contributing

Please see [CONTRIBUTING.md][contributing] for more information.

[chargely-homepage]: http://www.chargely.com
[composer-homepage]: https://getcomposer.org
[contributing]: https://github.com/chargely/chargify-sdk-php/blob/master/CONTRIBUTING.md
[documentation]: http://chargify-sdk-php.chargely.com
[issues]: https://github.com/chargely/chargify-sdk-php/issues