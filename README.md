Easy Rest Bundle
================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/36bd3d18-16b1-4cf2-8f58-53d940f67bf1/small.png)](https://insight.sensiolabs.com/projects/36bd3d18-16b1-4cf2-8f58-53d940f67bf1)

Simple and lightweight bundle provides JSON based request / response and exception handling support to develop RESTful API's with Symfony.

Features include:


* Listener for decoding JSON request body and accessing it from Request class
* `ParamConverter` for mapping JSON request to plain PHP object (using Symfony Serializer)
* Listener for creating JSON responses which is converts to JSON
    * Automatically determines correct HTTP status codes for DELETE and POST response
* Exception controller for providing error details 
    * Supports Symfony Validation errors
    * Provides stack-trace on development environment
* Supports Symfony 2 and 3
* Only uses plain listeners, easy to configure and disable certain features.

Not supports:

* XML serializer or format agnostic helpers
* Header format negotiation

Usage
============

JSON Request and Response
----------------------------------------

Responses are handled by `JsonResponseListener` listener. It's directly uses Symfony `JsonResponse` class for creating response. Simply you can use arrays or `JsonSerializable` objects. 

GET request and response:

    curl -i localhost:8000/user/12/details

Controller

    /**
     * @Route("/user/12/details")
     */
    public function getUserDetailsSample()
    {
        return [
            'user' => [
                'id' => '8f262cd7-9f2d-4bca-825e-e2444b1a57e0',
                'username' => 'o',
                'isEnabled' => true,
                'roles' => [
                    'ROLE_USER',
                    'ROLE_ADMIN'
                ]
            ]
        ];
    }

Response will be,
    
    HTTP/1.1 200 OK
    Cache-Control: no-cache, private
    Connection: close
    Content-Type: application/json

    {
        "user": {
            "id": "8f262cd7-9f2d-4bca-825e-e2444b1a57e0", 
            "isEnabled": true, 
            "roles": [
                "ROLE_USER", 
                "ROLE_ADMIN"
            ], 
            "username": "o"
        }
    }
    
Requests are handled by `RequestContentListener`, it tries to convert request body to array and wraps in `ParameterBag`. It's only activated for `POST`, `PUT` and `PATCH` requests. So, you can access to this parameters from Symfony Request object.

POST request example with JSON body:    

    curl -i -X POST \
      http://localhost:8000/access-tokens \
      -H 'content-type: application/json' \
      -d '{
    	"username": "o",
    	"password": "t0o53cur#",
    }'

In controller you can access parameters like:

    /**
     * @Method({"POST"})
     * @Route("/access-tokens")
     */
    public function createTokenAction(Request $request) {
        $username = $request->request->get('username'); // You will get 'o'
        $password = $request->request->get('password'); // You will get 't0o53cur#'
        
        ....
    }

##### TODO: Using JsonParamConverter

Working with exceptions and validation errors
------------------------------------------------------------

In default, an exception controller also converts handled exceptions to strict signature JSON responses with respective HTTP status codes.

    /**
     * @Route("/test/precondition-failed")
     */
    public function testPreconditionFailed(Request $request) {
        ...
        if (!$hasRequirements) {
            throw new PreconditionFailedHttpException('Invalid condition');
        }
    }
    
Response will be:

    $ curl -i localhost:8000/test/precondition-failed


    HTTP/1.1 412 Precondition Failed
    Cache-Control: no-cache, private
    Connection: close
    Content-Type: application/json

    {
        "code": 0, 
        "errors": [], 
        "message": "Invalid condition", 
        "status_code": 412, 
        "status_text": "Precondition Failed", 
        "trace": []
    }

In the `development` mode for the unhandled exceptions also a stack-trace is included in response.  

Validation errors and ExceptionWrapper
-----------------------------------------------------

For exceptions, this bundle comes with `ExceptionWrapper` for creating error responses in a nice way.

Using with Symfony Validator errors:

    /**
     * @Method({"POST"})
     * @Route("/access-tokens")
     */
    public function createTokenAction(Request $request) {
        ....

        $errors = $this->get('validator')->validate(
            $request->request->all(),
            new Assert\Collection(
                [
                    'username' => [
                        new Assert\NotBlank(),
                    ],
                    'password' => [
                        new Assert\NotBlank(),
                        new Assert\Length(['min' => 5]),
                    ],
                ]
            )
        );

        return (new ExceptionWrapper())
            ->setErrorsFromConstraintViolations($errors)
            ->setMessage(ErrorMessagesInterface::VALIDATION_ERROR)
            ->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->getResponse();
    }
    
An example request which is we expect to fail:

    curl -i -X POST \
      http://localhost:8000/access-tokens \
      -H 'content-type: application/json' \
      -d '{
    	"username": "",
    	"password": "t0o53cur#",
    	"extra_field": false
    }'

Response will be:

    HTTP/1.1 422 Unprocessable Entity
    Cache-Control: no-cache, private
    Connection: close
    Content-Type: application/json

    {
        "code": 0, 
        "errors": [
            {
                "message": "This value should not be blank.", 
                "path": "username"
            }, 
            {
                "message": "This field was not expected.", 
                "path": "extra_field"
            }
        ], 
        "message": "Validation Failed", 
        "status_code": 422, 
        "status_text": "Unprocessable Entity", 
        "trace": []
    }


You can also build your own custom error details:

    /**
     * @Route("/test/weird-error-test")
     */
    public function getWeirdErrors()
    {
        return (new ExceptionWrapper())
            ->setMessage('Something going wrong')
            ->setStatusCode(Response::HTTP_I_AM_A_TEAPOT)
            ->addError('foo', 'I don\'t expect an input like this')
            ->addError('bar', 'This should be an integer')
            ->getResponse();

    }

You will expect a response with this structure

    curl -i localhost:8000/test/weird-error-test


    HTTP/1.1 418 I'm a teapot
    Cache-Control: no-cache, private
    Connection: close
    Content-Type: application/json

    {
        "code": 0, 
        "errors": [
            {
                "message": "I don't expect an input like this", 
                "path": "foo"
            }, 
            {
                "message": "This should be an integer", 
                "path": "bar"
            }
        ], 
        "message": "Something going wrong", 
        "status_code": 418, 
        "status_text": "I'm a teapot", 
        "trace": []
    }


Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

    $ composer require osm/easy-rest-bundle "~1"

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

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


Step 3: Configuration
---------------------

Enable the bundle's configuration in `app/config/config.yml`:

    osm_easy_rest: ~

With default configuration, all listeners and exception controller will be enabled. You can
change this behaviour with following options:

    osm_easy_rest:
        enable_exception_listener: true
        enable_json_param_converter: false
        enable_json_response_listener: true
        enable_request_body_listener: true

License
-------

This bundle is distributed under the MIT license. See the complete license in the bundle.
