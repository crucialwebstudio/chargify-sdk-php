===============
Chargify Direct
===============

The ``Crucial\Service\ChargifyV2\Direct`` object provides the following methods.

.. contents::
   :depth: 2
   :local:

`Chargify Direct documentation <https://docs.chargify.com/chargify-direct-introduction>`_

Signup
------

Create a Chargify Direct signup form. See the `Chargify Documentation <https://docs.chargify.com/api-signups>`_ for more information.

.. code-block:: php

    $direct = $chargifyV2->direct();

    // set redirect
    $direct->setRedirect('https://example.local');

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
    ]]);

Now use ``$direct`` object to help create your HTML signup form.

.. code-block:: html

   <form accept-charset="utf-8" method="post" action="<?php echo $direct->getSignupAction() ?>">
        <?php echo $direct->getHiddenFields(); ?>

        <!-- the rest of your form goes here -->

   </form>

Card Update
-----------

Create a Chargify Direct card update form. See the `Chargify Documentation <https://docs.chargify.com/api-card-update>`_ for more information.

.. code-block:: php

    $direct = $chargifyV2->direct();

    // set redirect
    $direct->setRedirect('https://example.local');

    // set tamper-proof data
    $direct->setData([
        'subscription_id' => 1234
    ]);

Now use ``$direct`` object to help create your HTML card update form.

.. code-block:: html

   <!-- card update form for subscription ID 1234 -->
   <form accept-charset="utf-8" method="post" action="<?php echo $direct->getCardUpdateAction(1234) ?>">
        <?php echo $direct->getHiddenFields(); ?>

        <!-- the rest of your form goes here -->

   </form>

Validate Response Signature
---------------------------

The ``->isValidResponseSignature()`` method will test for a valid redirect to your site after a Chargify Direct signup
request.

.. code-block:: php

   $isValidResponse = $chargifyV2->direct()
        ->isValidResponseSignature($_GET['api_id'], $_GET['timestamp'], $_GET['nonce'], $_GET['status_code'], $_GET['result_code'], $_GET['call_id']);

   // $isValidResponse will be a boolean true\false.
   if ($isValidResponse) {
        // Do post-signup (or post card-update) tasks such as updating your database, sending a thank you email, etc.
   } else {
        // display an error message
   }