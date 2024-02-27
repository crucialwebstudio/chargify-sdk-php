========
Overview
========

Requirements
============

#. PHP >= 8.1.0

.. _installation:

Installation
============

Using `Composer <http://getcomposer.org>`_ is the recommended way to install the Chargify SDK for PHP. Composer is a
dependency management tool for PHP that allows you to declare the dependencies your project needs and installs them
into your project. In order to use the SDK with Composer, you must do the following:

Download and install Composer, if you don't already have it.

.. code-block:: bash

   curl -sS https://getcomposer.org/installer | php

Add ``chargely/chargify-sdk-php`` as a dependency in your project's composer.json file.

.. code-block:: bash

   php composer.phar require chargely/chargify-sdk-php:~0.1

Alternatively, you can specify Chargify SDK for PHP as a dependency in your project's existing composer.json file:

.. code-block:: js

   {
     "require": {
       "chargely/chargify-sdk-php": "~0.1"
     }
   }

Install your dependencies, including your newly added Chargify SDK for PHP.

.. code-block:: bash

   php composer.phar install

After installing, you need to require Composer's autoloader:

.. code-block:: php

   require 'vendor/autoload.php';

You can find out more on how to install Composer, configure autoloading, and
other best-practices for defining dependencies at `getcomposer.org <http://getcomposer.org>`_.


Bleeding edge
-------------

During your development, you can keep up with the latest changes on the master
branch by setting the version requirement for Chargify SDK for PHP to ``~0.1@dev``.

.. code-block:: js

   {
      "require": {
         "chargely/chargify-sdk-php": "~0.1@dev"
      }
   }


License
=======

Licensed using the `Apache 2.0 license <https://github.com/chargely/chargify-sdk-php/blob/master/LICENSE.md>`_.

    Copyright (c) 2016 Crucial Web Studio, LLC

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.


Contributing
============

We work hard to provide a high-quality and useful SDK for Chargify services, and
we greatly value feedback and contributions from our community. Please submit
your `issues <https://github.com/chargely/chargify-sdk-php/issues>`_ or `pull requests <https://github.com/chargely/chargify-sdk-php/pulls>`_ through GitHub.

Guidelines
----------

#. The SDK is released under the `Apache 2.0 license <https://github.com/chargely/chargify-sdk-php/blob/master/LICENSE.md>`_.
   Any code you submit will be released under that license. For substantial
   contributions, we may ask you to sign a `Contributor License Agreement (CLA) <https://github.com/chargely/chargify-sdk-php/blob/master/CLA.txt>`_.

#. The SDK has a minimum PHP version requirement of PHP 8.1. Pull requests must
   not require a PHP version greater than PHP 8.1 unless the feature is only
   utilized conditionally.

#. We follow all of the relevant PSR recommendations from the `PHP Framework Interop Group <http://php-fig.org>`_.
   Please submit code that follows these standards.
   The `PHP CS Fixer <http://cs.sensiolabs.org/>`_ tool can be helpful for formatting your code.

#. We maintain a high percentage of code coverage in our unit tests. If you make
   changes to the code, please add, update, and/or remove tests as appropriate.

#. If your code does not conform to the PSR standards or does not include
   adequate tests, we may ask you to update your pull requests before we accept
   them. We also reserve the right to deny any pull requests that do not align
   with our standards or goals.

#. If you would like to implement support for a significant feature that is not
   yet available in the SDK, please talk to us beforehand to avoid any
   duplication of effort.

In order to contribute, you'll need to checkout the source from GitHub and
install Chargify SDK fpr PHP's dependencies using Composer:

.. code-block:: bash

    git clone https://github.com/chargely/chargify-sdk-php.git
    cd chargify-sdk-php && curl -s http://getcomposer.org/installer | php && ./composer.phar install --dev

Running the tests
-----------------

The SDK is unit tested with PHPUnit. Run the tests using the following commands:

.. code-block:: bash

    cd tests/phpunit
    ../../vendor/bin/phpunit

    # with coverage report
    ../../vendor/bin/phpunit --coverage-html artifacts/coverage