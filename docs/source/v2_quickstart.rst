=================
API v2 Quickstart
=================

This page provides a quick introduction to Chargify SDK for PHP and introductory examples for
`v2 of the Chargify API <https://docs.chargify.com/api-introduction>`_. If you have not already installed the SDK,
head over to the :ref:`installation` page.

Making a Request
================

The first step to sending a request with the SDK is to create a ``Crucial\Service\ChargifyV2`` object.

Creating an API Client
----------------------

.. code-block:: php

    <?php
    use Crucial\Service\ChargifyV2;

    $chargifyV2 = new ChargifyV2([
        'api_id'       => '{{API_ID}}',
        'api_password' => '{{API_PASSWORD}}',
        'api_secret'   => '{{API_SECRET}}'
    ]);

The client constructor accepts an associative array of options:

``api_id``
    (string) Chargify Direct API ID.

``api_password``
    (string) Chargify Direct API password.

``api_secret``
    (string) Chargify Direct API secret.

See the `Chargify documentation <https://docs.chargify.com/chargify-direct-introduction#api-v2-authn>`_ for help
finding your API v2 credentials.

Resource Objects
----------------

The Chargify API v2 is divided into resources such as call, signups, card update, etc. The SDK follows this
same pattern and provides a simple interface for selecting the resource you want to operate on.

.. code-block:: php

    <?php
    use Crucial\Service\ChargifyV2;

    $chargifyV2 = new ChargifyV2([
        'api_id'       => '{{API_ID}}',
        'api_password' => '{{API_PASSWORD}}',
        'api_secret'   => '{{API_SECRET}}'
    ]);

    // Crucial\Service\ChargifyV2\Direct
    $direct = $chargifyV2->direct();

    // Crucial\Service\ChargifyV2\Call
    $call = $chargifyV2->call();

Sending Requests
----------------

Now that you have a resource object you're ready to send a request. The resource objects provide a fluent interface
for setting properties to send in your request. The example below shows how to fetch a call object.

.. code-block:: php

    <?php
    use Crucial\Service\ChargifyV2;

    $chargifyV2 = new ChargifyV2([
        'api_id'       => '{{API_ID}}',
        'api_password' => '{{API_PASSWORD}}',
        'api_secret'   => '{{API_SECRET}}'
    ]);

    // Crucial\Service\ChargifyV2\Call
    $call = $chargifyV2->call()
        // send request to fetch call object by ID
        ->readByChargifyId(1234);

Using Responses
===============

In the previous example, calling ``->readByChargifyId()`` on the ``Crucial\Service\ChargifyV2\Call`` object will send
the request to Chargify and load it with the call response.

You can access the response data as you would any normal array:

.. code-block:: php

    $callId   = $call['id']; // Chargify call ID
    $request  = $call['request'];
    $response = $call['response'];
    $success  = $call['success'];

Error Handling
==============

The SDK loads errors on the resource object for any errors that occur during a transfer.

- You can test for an error using the ``->isError()`` method of the resource object.

  .. code-block:: php

     if ($call->isError()) {
        // handle errors
     } else {
        // the transfer was successful
        $callId   = $call['id']; // Chargify call ID
        $request  = $call['request'];
        $response = $call['response'];
        $success  = $call['success'];
     }

- You can get the loaded errors, if any, using the ``->getErrors()`` method of the resource object.

  .. code-block:: php

     if ($call->isError()) {
        // array of errors loaded during the transfer
        $errors = $call->getErrors();
     }

.. note::

   In the above example, ``->isError()`` and ``->getErrors()`` only loads errors with the SDK's request to the Chargify
   API. It does not load errors from the call object itself, such as might be present in ``$call['response']['result']['errors']``.
