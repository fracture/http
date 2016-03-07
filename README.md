# Fracture\Http


[![Build Status](https://travis-ci.org/fracture/http.png?branch=master)](https://travis-ci.org/fracture/http)
[![Code Coverage](https://scrutinizer-ci.com/g/fracture/http/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/fracture/http/?branch=master)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/fracture/http.svg)](https://scrutinizer-ci.com/g/fracture/http/?branch=master)
[![Packagist](https://img.shields.io/packagist/v/fracture/http.svg)](https://packagist.org/packages/fracture/http)

## Introduction

A simple abstraction for handling the HTTP request and responses. Library is made for interacting with [Fracture\Routing](https://github.com/fracture/http) and provides simple object-oriented abstractions.

## Installation

You can add the library to your project using composer with following command:

```sh
composer require fracture/http
```


##Usage

All of the following code will assume that the Composer's autoloader has already been included.

###Basic request initialization

While initializing a new `Request` instance manually is possible, for the instance to be fully prepared, it require several additional steps. For this reason it's better to use the `RequestBuider`, that will those steps:

```php
<?php
// -- unimportant code above --

$builder = new Fracture\Http\RequestBuilder;
$request = $builder->create([
    'get'    => $_GET,
    'files'  => $_FILES,
    'server' => $_SERVER,
    'post'   => $_POST,
    'cookies'=> $_COOKIE,
]);
```

Use of this code fragment is sufficient for any basic website and will produces a ready-to-use `Request` instance.

###Requests and REST

When creating a site, that provides REST API, a common practice is to implement API versioning via HTTP Accept and Content-Type headers. To retrieve data, which was sent with a custom Content-Type header, you define a parser, which, if the media type matches, is executed to supplement the `Request` instance with additional parameters.

```php
<?php
// -- unimportant code above --

$builder = new Http\RequestBuilder;
$builder->addContentParser('application/json', function () {
    $data =  json_decode(file_get_contents('php://input'), true);

    if ($data === null) {
        $data = [];
    }

    return $data;
});
$request = $builder->create([
    'server' => $_SERVER,
    'cookies'=> $_COOKIE,
]);
```

Also the `RequestBuilder` instance can have multiple content parsers added.

####Content parsers

A parser is defined as an anonymous function, which will be called with `Fracture\Http\Headers\ContentType` and `Fracture\Http\Request` instances as parameters and is expected to return an array of `name => value` pairs for parameters.

```
array function([ Fracture\Http\Headers\ContentType $header [, Fracture\Http\Request $request]])
```

You can also use content parsers to override `Request` attributes. For example, if you want to alter the request method, when submitting for with "magic" parameter like `<input type="hidden" name="_my_method" value="PUT" />` (which is a common approach for making more RESTful and bypass the limitations of standard webpage):

```php
<?php
// -- unimportant code above --

$builder->addContentParser('*/*', function ($header, $request) {
    $override = $request->getParameter('_my_method');
    if ($override) {
        $request->setMethod($override);
    }
    return [];
});
```


###Accessing data in the request

When the instance of `Request` has been fully initialized (using `RequestBuilder`), you gain ability to extract several types of information from this abstraction:

####Parameters



####Cookies
####File uploads
####Request method
####Headers
