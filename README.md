# Fracture\Http


[![Build Status](https://travis-ci.org/fracture/http.png?branch=master)](https://travis-ci.org/fracture/http)
[![Code Coverage](https://scrutinizer-ci.com/g/fracture/http/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/fracture/http/?branch=master)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/fracture/http.svg)](https://scrutinizer-ci.com/g/fracture/http/?branch=master)
[![Packagist](https://img.shields.io/packagist/v/fracture/http.svg)](https://packagist.org/packages/fracture/http)

## Introduction

A simple abstraction for handling the HTTP requests and responses. Library is made for interacting with [**fracture\routing**](https://github.com/fracture/routing) and provides simple object-oriented interface.

## Installation

You can add the library to your project using composer with following command:

```sh
composer require fracture/http
```


##Usage

All of the following code will assume that the Composer's autoloader has already been included.

###Basic request initialization

While initializing a new `Request` instance manually is possible, for said instance to be fully prepared, it require several additional steps. To simplify this process, you should use `RequestBuider`, which will perform all of those steps:

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

When creating a site, that provides REST API, a common practice is to implement API versioning via HTTP Accept and Content-Type headers. But, if you send a `POST` query with custom Content-Type header, PHP will not populate `$_POST` with the information, that you sent to the server. And there are no `$_PUT` and `$_DETETE` superglobals in PHP.

To retrieve data information in a usable form, you have define a content parser, which, if the media type matches, is executed to supplement the `Request` instance with additional parameters.

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

It is possible for `RequestBuilder` instance to have multiple content parsers added.

####Content parsers

A content parser is defined as an anonymous function, which will be executed with `Fracture\Http\Headers\ContentType` and `Fracture\Http\Request` instances as parameters and is expected to return an array of `name => value` pairs for parameters.

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

When the instance of `Request` has been fully initialized (preferably, using `RequestBuilder`), you gain ability to extract several types of information from this abstraction.

####Parameters

To retrieve a parameter from an initialized `Request` instance you have to use `getParameter()` method:

```php
<?php
// -- unimportant code above --

$id = $request->getParameter('id');
```

When instance has been produced using `RequestBuilder`, it will contain parameter values from `$_GET`, `$_POST` and the data from content parsers.

> **Important!**  
> If your code contains two parameters in `$_GET` and `$_POST` with same name, it will trigger a warning. Same warning will also be triggered, if one of the content parsers returned parameter, which already existed in `$_GET` or `$_POST`. The library is designed with an assumption, that having such overlap is an unintentional mistake.


The `getParameter()` method has the following signature:

```
mixed Request::getParameter( string $name );
```

If parameter with the provided name does not exist, the method will return `null`.

####Cookies

Retrieval of cookies is done using  `getCookies()` method.

```php
<?php
// -- unimportant code above --

$token = $request->getCookie('token');
```
The `getCookie()` method has the following signature:

```
mixed Request::getCookie( string $name );
```

If parameter with the provided name does not exist, the method will return `null`.

####File uploads

The method for retrieving uploaded files from `Request` instance is `getUpload()`:

```php
<?php
// -- unimportant code above --

$file = $request->getUpload('attachment');
```

This method is called with the input's name as parameter. Depending on the structure of your upload form, it will return either an instance of `UploadedFile`, if your HTML had `name="attachment"`, or `FileBag` instance, if form element had `name="attachment[]"`.

If no file has been uploaded with the given field name, then the method will return `null`.

The method signature is:

```
mixed Request::getUpload( string $name );
```

####Request method

To retrieve the HTTP request method, you have to use `getMethod()` method.

```php
<?php
// -- unimportant code above --

$method = $request->getMethod();
```

It has the following signature:

```
string Request::getMethod( void );
```


####Headers

Currently the `Request` instance lets you access abstractions for Accept and Content-Type HTTP headers.  It can be done using  `getAcceptHeader()` and `getContentTypeHeader()` methods. These methods have the following signatures:

```
Fracture\Http\Headers\Accept Request::getAcceptHeader( void )
```
and
```
Fracture\Http\Headers\ContentType Request::getContentTypeHeader( void )
```
