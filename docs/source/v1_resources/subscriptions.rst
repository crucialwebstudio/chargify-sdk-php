=============
Subscriptions
=============

The ``Crucial\Service\Chargify\Subscription`` object provides the following methods.

.. contents::
   :depth: 2
   :local:

`Chargify API documentation for subscriptions <https://docs.chargify.com/api-subscriptions>`_

Subscription Output Attributes
------------------------------

Many of the examples below will result in the following standard attributes on the ``$subscription``
object.

.. code-block:: json

   {
        "id":[@subscription.id],
        "state":"active",
        "previous_state":`auto generated`,
        "balance_in_cents":0,
        "total_revenue_in_cents":1000,
        "product_price_in_cents": 1000,
        "product_version_number": 1,
        "current_period_started_at":`auto generated`,
        "current_period_ends_at":`auto generated`,
        "next_assessment_at":`auto generated`,
        "activated_at":`auto generated`,
        "trial_ended_at":`auto generated`,
        "trial_started_at":`auto generated`,
        "expires_at":`auto generated`,
        "created_at":`auto generated`,
        "updated_at":`auto generated`,
        "cancellation_message":null,
        "canceled_at":`your value`,
        "cancel_at_end_of_period":false,
        "delayed_cancel_at":null,
        "coupon_code":`your value`,
        "signup_payment_id":`auto generated`,
        "signup_revenue":`your value`,
        "payment_collection_method":"automatic",
        "current_billing_amount_in_cents": "1000",
        "customer":{
          "id":`auto generated`,
          "first_name":`your value`,
          "last_name":`your value`,
          "email":`your value`,
          "organization":`your value`,
          "reference":`your value`,
          "address":`your value`,
          "address_2":`your value`,
          "city":`your value`,
          "state":`auto generated`,
          "zip":`auto generated`,
          "country":`auto generated`,
          "phone":`auto generated`,
          "updated_at":`auto generated`,
          "created_at":`auto generated`
        },
        "product":{
          "id":`auto generated`,
          "name":`your value`,
          "handle":`your value`,
          "description":`your value`,
          "price_in_cents":`your value`,
          "accounting_code":`your value`,
          "interval":`your value`,
          "interval_unit":`your value`,
          "initial_charge_in_cents":null,
          "trial_price_in_cents":null,
          "trial_interval":null,
          "trial_interval_unit":null,
          "expiration_interval_unit":null,
          "expiration_interval":null,
          "return_url":null,
          "update_return_url":null,
          "return_params":null,
          "require_credit_card":true,
          "request_credit_card":true,
          "created_at":`auto generated`,
          "updated_at":`auto generated`,
          "archived_at":null,
          "product_family":{
            "id":`auto generated`,
            "name":`your value`,
            "handle":`your value`,
            "accounting_code":`your value`,
            "description":`your value`
          }
        },
        "credit_card":{
          "id":`auto generated`,
          "first_name":`your value`,
          "last_name":`your value`,
          "masked_card_number":`your value`,
          "card_type":`auto generated`,
          "expiration_month":`your value`,
          "expiration_year":`your value`,
          "billing_address":`your value`,
          "billing_address_2":`your value`,
          "billing_city":`your value`,
          "billing_state":`your value`,
          "billing_zip":`your value`,
          "billing_country":`your value`,
          "current_vault":`your value`,
          "vault_token":`your value`,
          "customer_vault_token":`your value`,
          "customer_id":`auto generated`
        }
      }

Create
------

Create a new subscription in Chargify.

.. code-block:: php

    $subscription = $chargify->subscription()
        // product ID being signed up for
        ->setProductId(123)

        // alternatively, set the product by handle
        //->setProductHandle('my-product-handle')

        // customer attributes
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

        // alternatively, set customer ID or reference if the new subscription is for an existing customer
        //->setCustomerId(1234)
        //->setCustomerReference('customer-reference')

        // payment profile attributes
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

        // (Optional, used for Subscription Import)
        //->setNextBillingAt('8/6/2010 11:34:00 EDT')

        // send the request
        ->create();

Create: Output Attributes
~~~~~~~~~~~~~~~~~~~~~~~~~

Standard subscription output attributes.

Read
----

Read a single existing subscription.

.. code-block:: php

   $subscription = $chargify->subscription()
        ->read($existingSubscriptionId);

Read: Output Attributes
~~~~~~~~~~~~~~~~~~~~~~~

Standard subscription output attributes.

List
----

List all subscriptions for the Chargify site you are working with.

Listing subscriptions is paginated, 2000 at a time, by default. They are listed most recently created first.
You may control pagination using the ``->setPage()`` and ``->setPerPage()`` methods.

.. code-block:: php

   $subscription = $chargify->subscription()
        ->setPage(1)
        ->setPerPage(100)
        ->listSubscriptions();

List: Output Attributes
~~~~~~~~~~~~~~~~~~~~~~~

A zero-indexed array of subscriptions, each with the standard subscription output attributes.

List By Customer
----------------

List all subscriptions for a given customer.

.. code-block:: php

   $subscription = $chargify->subscription()
        // list subscriptions for customer ID 1234
        ->listByCustomer(1234);

List By Customer: Output Attributes
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

A zero-indexed array of subscriptions, each with the standard subscription output attributes.

Update
------

Update a subscription's product, customer attributes, or payment profile attributes.

.. code-block:: php

    $subscription = $chargify->subscription()
        // changing the product on an existing subscription will result in a non-prorated migration.
        ->setProductId(123)

        // alternatively, set the product by handle
        //->setProductHandle('my-product-handle')

        // new customer attributes
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

        // new payment profile attributes
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

        // send the request
        ->create();

Update: Output Attributes
~~~~~~~~~~~~~~~~~~~~~~~~~

Standard subscription output attributes.

Migrate
-------

Perform a prorated migration on a subscription. See `Chargify Documentation <https://docs.chargify.com/api-migrations>`_
for more details.

.. code-block:: php

   $subscription = $chargify->subscription()
        // set new product ID
        ->setProductId(1234)

        // alternatively, set new product by handle
        //->setProductHandle('product-handle')

        // (optional) Include trial in migration. 1 for yes, 0 for no. default: 0
        //->setIncludeTrial(1)

        // (optional) Include initial charge in migration. 1 for yes, 0 for no. default: 0
        //->setIncludeInitialCharge(1)

        // send the migration request
        ->migrate();

Migrate: Output Attributes
~~~~~~~~~~~~~~~~~~~~~~~~~~

Standard subscription output attributes.

Cancel Immediately
------------------

Cancel a subscription immediately in Chargify.

.. code-block:: php

   $subscription = $chargify->subscription()

        // (optional) Set cancellation message.
        //->setCancellationMessage('No longer using the service')

        // cancel subscription ID 1234 immediately
        ->cancelImmediately(1234);

Cancel Immediately: Output Attributes
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Standard subscription output attributes.

Cancel Delayed
--------------

Cancel a subscription at the end of the current billing period.

.. code-block:: php

   $subscription = $chargify->subscription()

        // (optional) Set cancellation message.
        //->setCancellationMessage('No longer using the service')

        // cancel subscription ID 1234 immediately
        ->cancelDelayed(1234);

Cancel Delayed: Output Attributes
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Standard subscription output attributes.

Reactivate
----------

Reactivate an inactive subscription.

.. code-block:: php

   $subscription = $chargify->subscription()

        // (optional) Include trial period (if any) when the subscription is re-activated
        //->setIncludeTrial(true)

        // reactivate subscription ID 1234
        ->reactivate(1234);

Reactivate: Output Attributes
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Standard subscription output attributes.

Reset Balance
-------------

Reset the balance of a subscription to zero.

.. code-block:: php

   $subscription = $chargify->subscription()
        // reset balance to zero on subscription ID 1234
        ->resetBalance(1234);

Reset Balance: Output Attributes
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Standard subscription output attributes.