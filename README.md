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

When creating a site, that provides REST API, a common practice is to implement API versioning via HTTP Accept and Content-Type headers.

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
