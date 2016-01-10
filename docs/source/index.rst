.. title:: Chargify API client for PHP

==================================
Chargify SDK for PHP Documentation
==================================

Chargify SDK for PHP is a PHP package that makes it easy to interact with the Chargify API. It has been used in
production for years on your flagship product, `Chargely, a hosted billing portal for Chargify <http://www.chargely.com>`_.

- Abstracts away underlying HTTP requests to the Chargify API
- Supports Chargify API v1 and Chargify Direct (v2)
- Well documented
- Unit tested

.. code-block:: php

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

User Guide
==========

.. toctree::
   :maxdepth: 2

   overview
   v1_quickstart
   v1_resources/index
   v2_quickstart
   v2_resources/index
