# Contributing to the Chargify SDK for PHP

We work hard to provide a high-quality and useful SDK for Chargify services, and
we greatly value feedback and contributions from our community. Please submit
your [issues][] or [pull requests][pull-requests] through GitHub.

## Things to keep in mind

1. The SDK is released under the [Apache license][license]. Any code you submit
   will be released under that license. For substantial contributions, we may
   ask you to sign a [Contributor License Agreement (CLA)][cla].
1. We follow all of the relevant PSR recommendations from the [PHP Framework
   Interop Group][php-fig]. Please submit code that follows these standards.
   The [PHP CS Fixer][cs-fixer] tool can be helpful for formatting your code.
1. We maintain a high percentage of code coverage in our unit tests. If you make
   changes to the code, please add, update, and/or remove tests as appropriate.
1. If your code does not conform to the PSR standards or does not include
   adequate tests, we may ask you to update your pull requests before we accept
   them. We also reserve the right to deny any pull requests that do not align
   with our standards or goals.
1. If you would like to implement support for a significant feature that is not
   yet available in the SDK, please talk to us beforehand to avoid any
   duplication of effort.
   
# Running tests

    cd tests/phpunit
    ../../vendor/bin/phpunit
    
    # with coverage report
    ../../vendor/bin/phpunit --coverage-html artifacts/coverage

[issues]: https://github.com/chargely/chargify-sdk-php/issues
[pull-requests]: https://github.com/chargely/chargify-sdk-php/pulls
[license]: https://github.com/chargely/chargify-sdk-php/blob/master/LICENSE.md
[cla]: https://github.com/chargely/chargify-sdk-php/blob/master/CLA.txt
[php-fig]: http://php-fig.org
[cs-fixer]: http://cs.sensiolabs.org/