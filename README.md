# Chargify SDK for PHP

[![Total Downloads](https://img.shields.io/packagist/dt/chargely/chargify-sdk-php.svg?style=flat)](https://packagist.org/packages/chargely/chargify-sdk-php)
[![Build Status](https://img.shields.io/travis/chargely/chargify-sdk-php.svg?style=flat)](https://travis-ci.org/chargely/chargify-sdk-php)
[![Latest Stable Version](https://img.shields.io/packagist/v/chargely/chargify-sdk-php.svg?style=flat)](https://packagist.org/packages/chargely/chargify-sdk-php)
[![Apache 2 License](https://img.shields.io/packagist/l/chargely/chargify-sdk-php.svg?style=flat)](https://github.com/chargely/chargify-sdk-php/blob/master/LICENSE.md)

This library helps you interact with the Chargify API using PHP. It has been used in production for many years by our 
flagship product, [Chargley, a billing portal for Chargify][chargely-homepage].

# Installation

Using [Composer][composer-homepage] is the recommended way to install the Chargify SDK for PHP. Composer is a 
dependency management tool for PHP that allows you to declare the dependencies your project needs and installs them 
into your project. In order to use the SDK with Composer, you must do the following:

1. Add "chargely/chargify-sdk-php" as a dependency in your project's composer.json file.

        {
            "require": {
                "chargely/chargify-sdk-php": "0.0.5"
            }
        }

2. Download and install Composer, if you don't already have it.

        curl -sS https://getcomposer.org/installer | php

3. Install your dependencies, including your newly added Chargify SDK for PHP.

        php composer.phar install

4. Require Composer's autoloader

        require '/path/to/vendor/autoload.php';

# Chargify Direct Usage

## Create a signup form [Docs](https://docs.chargify.com/chargify-direct-signups)

    <?php
    require '/path/to/vendor/autoload.php';
    
    use Crucial\Service\ChargifyV2;

    $chargifyV2 = new ChargifyV2([
        'api_id'       => 'gfdjgjfdklsjgsl',
        'api_password' => 'mdnmkfvmx',
        'api_secret'   => 'jdksljfklds;a'
    ]);
    $direct = $chargifyV2->direct();
    
    // set redirect
    $direct->setRedirect('http://example.local');
    
    // set tamper-proof data
    $direct->setData([
        'signup' => [
            'product'  => [
                'id' => 1234
            ],
            'customer' => [
                'first_name' => 'Dan',
                'last_name'  => 'Bowen',
                'email'      => 'foo@mailinator.com'
            ]
        ]
    ]);
    ?>
    
    <form accept-charset="utf-8" method="post" action="<?php echo $direct->getSignupAction() ?>">
        <?php echo $direct->getHiddenFields(); ?>
        
        ....
    </form>

# API Usage

## Create a client instance

    require '/path/to/vendor/autoload.php';
    
    use Crucial\Service\Chargify;
    
    $chargify = new Chargify([
        'hostname'   => 'yoursubdomain.chargify.com',
        'api_key'    => '{{API_KEY}}',
        'shared_key' => '{{SHARED_KEY}}'
    ]);
    
## Create a subscription [Docs](https://docs.chargify.com/api-customers)

    $subscription = $chargify->subscription()
        ->setProductId(123)
        ->setCustomerAttributes([
            'first_name'   => '{{FIRST_NAME}}',
            'last_name'    => '{{LAST_NAME}}',
            'email'        => '{{EMAIL}}',
            'organization' => '{{ORGANIZATION}}',
            'phone'        => '{{PHONE}}',
            'address'      => '{{ADDRESS}}',
            'address_2'    => '{{ADDRESS_2}}',
            'city'         => '{{CITY}}',
            'state'        => '{{STATE}}',
            'zip'          => '{{ZIP}}',
            'country'      => '{{COUNTRY}}',
        ])
        ->setPaymentProfileAttributes([
            'first_name'       => '{{FIRST_NAME}}',
            'last_name'        => '{{LAST_NAME}}',
            'full_number'      => '{{CC_NUMBER}}',
            'expiration_month' => '{{EXPIRY_MONTH}}',
            'expiration_year'  => '{{EXPIRY_YEAR}}',
            'cvv'              => '{{CVV}}',
            'billing_address'  => '{{ADDRESS}}',
            'billing_city'     => '{{CITY}}',
            'billing_state'    => '{{STATE}}',
            'billing_zip'      => '{{ZIP}}',
            'billing_country'  => '{{COUNTRY}}'
        ])
        ->create();
        
    // check for an error
    if ($subscription->isError()) {
        // access errors for debugging
        var_dump($subscription->getErrors());
    } else {
        echo "Subscription ID: " . $subscription['id'] . PHP_EOL;
        echo "Customer Email: " . $subscription['customer']['email'] . PHP_EOL;
    }
    
# Contributing

Please see [CONTRIBUTING.md][contributing] for more information.

[chargely-homepage]: http://www.chargely.com
[composer-homepage]: https://getcomposer.org
[contributing]: https://github.com/chargely/chargify-sdk-php/blob/master/CONTRIBUTING.md