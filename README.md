Easy Rest Bundle
================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/36bd3d18-16b1-4cf2-8f58-53d940f67bf1/small.png)](https://insight.sensiolabs.com/projects/36bd3d18-16b1-4cf2-8f58-53d940f67bf1)

Simple and lightweight bundle provides JSON based request / response and exception handling support to develop RESTful API's with Symfony.

Features include:

* Listener for accessing JSON request body from Request class as an array
* Listener for creating JSON Response which is converts array response to JSON
    * Automatically determines correct HTTP status codes for DELETE and POST response
* Exception controller for providing error details 
    * Supports Symfony Validation errors
    * Provides stack-trace on development environment
* Supports Symfony 2 and 3

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require osm/easy-rest-bundle "~1"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Osm\EasyRestBundle\OsmEasyRestBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Configuration
---------------------

Enable the bundle's configuration in `app/config/config.yml`:

    osm_easy_rest: ~

With default configuration, listeners and exception controller will be enabled. You can
change this behaviour with following parameters:

    osm_easy_rest:
        enable_listeners: false
        enable_exception_controller: true

License
-------

This bundle is distributed under the MIT license. See the complete license in the bundle.
