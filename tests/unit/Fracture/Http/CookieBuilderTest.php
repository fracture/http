<?php


namespace Fracture\Http;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class CookieBuilderTest extends PHPUnit_Framework_TestCase
{


    public function testCreatingDefaultCookie()
    {
        $instance = new CookieBuilder;
        $cookie = $instance->create('foo', 'bar');
        $this->assertInstanceOf('Fracture\Http\Cookie' , $cookie);
    }


    public function testValueAndNameOfCreateCookie()
    {
        $instance = new CookieBuilder;
        $cookie = $instance->create('foo', 'bar');
        $this->assertSame('foo' , $cookie->getName());
        $this->assertSame('bar' , $cookie->getValue());
    }


    public function testCompleteDefaultSetupForCookie()
    {
        $instance = new CookieBuilder;
        $cookie = $instance->create('foo', 'bar');
        $this->assertEquals([
            'expires' => null,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httpOnly' => true,
        ], $cookie->getParameters());
    }


    public function testResultWhenAlteringBuildersPamameters()
    {
        $instance = new CookieBuilder;
        $cookie = $instance->create('foo', 'bar');
        $this->assertEquals([
            'expires' => null,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httpOnly' => true,
        ], $cookie->getParameters());
    }
}
