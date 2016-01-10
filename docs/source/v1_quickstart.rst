=================
API v1 Quickstart
=================

This page provides a quick introduction to Chargify SDK for PHP and introductory examples for
`v1 of the Chargify API <https://docs.chargify.com/api-introduction>`_. If you have not already installed the SDK,
head over to the :ref:`installation` page.

Making a Request
================

The first step to sending a request with the SDK is to create a ``Crucial\Service\Chargify`` object.

Creating an API Client
----------------------

.. code-block:: php

    <?php
    use Crucial\Service\Chargify;

    $chargify = new Chargify([
        'hostname'   => 'yoursubdomain.chargify.com',
        'api_key'    => '{{API_KEY}}',
        'shared_key' => '{{SHARED_KEY}}'
    ]);

The client constructor accepts an associative array of options:

``hostname``
    (string) The hostname of the Chargify site you want to interact with.

``api_key``
    (string) API key from your Chargify site.

``shared_key``
    (string) Shared key from your Chargify site.

See the `Chargify documentation <https://docs.chargify.com/api-authentication>`_ for help finding your API v1 credentials.

Resource Objects
----------------

The Chargify API v1 is divided into resources such as subscriptions, customers, products, etc. The SDK follows this
same pattern and provides a simple interface for selecting the resource you want to operate on.

.. code-block:: php

    <?php
    use Crucial\Service\Chargify;

    $chargify = new Chargify([
        'hostname'   => 'yoursubdomain.chargify.com',
        'api_key'    => '{{API_KEY}}',
        'shared_key' => '{{SHARED_KEY}}'
    ]);

    // Crucial\Service\Chargify\Customer
    $customer = $chargify->customer();

    // Crucial\Service\Chargify\Subscription
    $subscription = $chargify->subscription();

    // Crucial\Service\Chargify\Product
    $product = $chargify->product();

    // Crucial\Service\Chargify\Adjustment
    $adjustment = $chargify->adjustment();

    // Crucial\Service\Chargify\Charge
    $charge = $chargify->charge();

    // Crucial\Service\Chargify\Component
    $component = $chargify->component();

    // Crucial\Service\Chargify\Coupon
    $coupon = $chargify->coupon();

    // Crucial\Service\Chargify\Transaction
    $transaction = $chargify->transaction();

    // Crucial\Service\Chargify\Refund
    $refund = $chargify->refund();

    // Crucial\Service\Chargify\Statement
    $statement = $chargify->statement();

    // Crucial\Service\Chargify\Event
    $event = $chargify->event();

    // Crucial\Service\Chargify\Webhook
    $webhook = $chargify->webhook();

    // Crucial\Service\Chargify\Stats
    $stats = $chargify->stats();

Sending Requests
----------------

Now that you have a resource object you're ready to send a request. The resource objects provide a fluent interface
for setting properties to send in your request. The example below shows how to create a new customer.

.. code-block:: php

    <?php
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

Using Responses
===============

In the previous example, calling ``->create()`` on the ``Crucial\Service\Chargify\Customer`` object will send the
request to Chargify and load it with the newly created customer response.

You can access the response data as you would any normal array:

.. code-block:: php

    $customerId = $customer['id']; // Chargify customer ID
    $firstName  = $customer['first_name'];
    $lastName   = $customer['last_name'];
    $email      = $customer['email'];

Error Handling
==============

The SDK loads errors on the resource object for any errors that occur during a transfer.

- You can test for an error using the ``->isError()`` method of the resource object.

  .. code-block:: php

     if ($customer->isError()) {
        // handle errors
     } else {
        // the transfer was successful
        $customerId = $customer['id']; // Chargify customer ID
        $firstName  = $customer['first_name'];
        $lastName   = $customer['last_name'];
        $email      = $customer['email'];
     }

- You can get the loaded errors, if any, using the ``->getErrors()`` method of the resource object.

  .. code-block:: php

     if ($customer->isError()) {
        // array of errors loaded during the transfer
        $errors = $customer->getErrors();
     }
