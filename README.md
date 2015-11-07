# Chargify SDK for PHP

[![Build Status](https://travis-ci.org/chargely/chargify-sdk-php.svg?branch=master)](https://travis-ci.org/chargely/chargify-sdk-php)
[![Latest Stable Version](https://poser.pugx.org/chargely/chargify-sdk-php/v/stable)](https://packagist.org/packages/chargely/chargify-sdk-php)
[![Total Downloads](https://poser.pugx.org/chargely/chargify-sdk-php/downloads)](https://packagist.org/packages/chargely/chargify-sdk-php)
[![License](https://poser.pugx.org/chargely/chargify-sdk-php/license)](https://packagist.org/packages/chargely/chargify-sdk-php)

This library helps you interact with the Chargify API using PHP. It has been used in production for many years by our 
flagship product, [Chargley, a billing portal for Chargify](http://www.getchargely.com).

# Installation

Using [Composer](https://getcomposer.org/) is the recommended way to install the Chargify SDK for PHP. Composer is a 
dependency management tool for PHP that allows you to declare the dependencies your project needs and installs them 
into your project. In order to use the SDK with Composer, you must do the following:

1. Add "crucialwebstudio/chargify-sdk-php" as a dependency in your project's composer.json file.

        {
            "require": {
                "chargely/chargify-sdk-php": "0.0.4"
            }
        }

2. Download and install Composer, if you don't already have it.

        curl -sS https://getcomposer.org/installer | php

3. Install your dependencies, including your newly added Chargify SDK for PHP.

        php composer.phar install

4. Require Composer's autoloader

        require '/path/to/vendor/autoload.php';

# Usage

## Create a client instance

    require '/path/to/vendor/autoload.php';
    
    use Crucial\Service\Chargify;
    
    $chargify = new Chargify(array(
        'hostname'   => 'yoursubdomain.chargify.com',
        'api_key'    => '{{API_KEY}}',
        'shared_key' => '{{SHARED_KEY}}'
    ));
    
## Create a subscription [Docs](https://docs.chargify.com/api-customers)

    $subscription = $chargify->subscription()
        ->setProductId(123)
        ->setCustomerAttributes(array(
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
        ))
        ->setPaymentProfileAttributes(array(
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
        ))
        ->create();
        
    // check for an error
    if ($subscription->isError()) {
        // access errors for debugging
        var_dump($subscription->getErrors());
    } else {
        echo "Subscription ID: " . $subscription['id'] . PHP_EOL;
        echo "Customer Email: " . $subscription['customer']['email'] . PHP_EOL;
    }
    
# Running tests

    cd tests/phpunit
    ../../vendor/bin/phpunit
    
    # with coverage report
    ../../vendor/bin/phpunit --coverage-html artifacts/coverage